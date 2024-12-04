<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../php/cerrarSession.php");
    exit();
}

require '../php/conexion.php';
require_once '../php/functions.php';

$id_camarero = $_SESSION['user_id'];
$info_waiter = get_info_waiter_bbdd($conn, $id_camarero);

$conditions = "WHERE o.estado_ocupacion = 'Registrada'";
$order_by = "";
$params = [];
$param_types = "";

// Verificamos que use método POST y use el botón del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && filter_has_var(INPUT_POST, 'filtrosBuscando')) {
    $filters = [];
    
    $fields = [
        'id_reserva' => ['field' => 'o.id_ocupacion', 'type' => 'i'],
        'nombre_camarero' => ['field' => 'c.nombre_camarero', 'type' => 's', 'like' => true],
        'apellido_camarero' => ['field' => 'c.apellidos_camarero', 'type' => 's', 'like' => true],
        'id_mesa' => ['field' => 'm.id_mesa', 'type' => 'i'],
        'ubicacion_sala' => ['field' => 's.ubicacion_sala', 'type' => 's', 'like' => true],
        'fecha_inicio' => ['field' => 'o.fecha_inicio', 'type' => 's', 'operator' => '<='],
        'fecha_final' => ['field' => 'o.fecha_final', 'type' => 's', 'operator' => '>=']
    ];
    
    foreach ($fields as $post_key => $db_field) {
        $input_value = htmlspecialchars($_POST[$post_key]) ?? '';
        if (!empty($input_value)) {
            $operator = $db_field['operator'] ?? '=';
            $like = $db_field['like'] ?? false;
            $filters[] = $db_field['field'] . " " . ($like ? "LIKE" : $operator) . " ?";
            $param_types .= $db_field['type'];
            $params[] = $like ? "%{$input_value}%" : $input_value;
        }
    }

    if (!empty($filters)) {
        $conditions .= " AND " . implode(' AND ', $filters);
    }

    $allowed_columns = ['id_ocupacion', 'nombre_camarero', 'id_mesa', 'ubicacion_sala', 'fecha_inicio', 'fecha_final'];
    $ordenar_nombre_columna = $_POST['column_name'] ?? '';
    $ordenar_por = $_POST['ordenar_registro'] ?? '';

    if (in_array($ordenar_nombre_columna, $allowed_columns)) {
        $order_direction = ($ordenar_por === 'Ascendente') ? 'ASC' : 'DESC';
        $order_by = "ORDER BY {$ordenar_nombre_columna} {$order_direction}";
    }
}

$query = "
    SELECT 
        o.id_ocupacion,
        c.nombre_camarero,
        c.apellidos_camarero,
        m.id_mesa,
        s.ubicacion_sala,
        o.fecha_inicio,
        o.fecha_final,
        o.estado_ocupacion
    FROM 
        tbl_ocupacion o
    INNER JOIN 
        tbl_camarero c ON o.id_camarero = c.id_camarero
    INNER JOIN 
        tbl_mesa m ON o.id_mesa = m.id_mesa
    INNER JOIN 
        tbl_sala s ON m.id_sala = s.id_sala
    $conditions $order_by";

$stmt_register = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt_register, $query)) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt_register, $param_types, ...$params);
    }
    mysqli_stmt_execute($stmt_register);
    $result = mysqli_stmt_get_result($stmt_register);
} else {
    die("Error preparing statement: " . mysqli_error($conn));
}

mysqli_stmt_close($stmt_register);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Reservas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/historicoResponsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Cabecera -->
    <header id="container_header" class="d-flex">
        <div id="container-username" class="d-flex align-items-center">
            <div id="icon_profile_header">
                <img src="../img/logoSinFondo.png" alt="Logo" id="icon_profile" class="img-fluid">
            </div>
            <div id="username_profile_header" class="ms-3">
                <p id="p_username_profile"><?php echo htmlspecialchars($info_waiter['username']); ?></p>
                <span class="span_subtitle"><?php echo htmlspecialchars($info_waiter['name']) . " " . htmlspecialchars($info_waiter['surname']); ?></span>
            </div>
        </div>

        <div id="container_title_header" class="text-center">
            <h1 id="title_header" class="m-0"><strong>Dinner At Westfield</strong></h1>
            <span class="span_subtitle">Gestión de mesas</span>
        </div>

        <nav id="nav_header">
            <a href="./mesas.php" class="btn btn-danger me-2 btn_custom_logOut">Reservar mesas</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header> 

    <!-- Contenido principal -->
    <main class="container mt-5">
        <?php if (mysqli_num_rows($result) == 0 && !filter_has_var(INPUT_POST, 'filtrosBuscando')): ?>
            <div id="bind_result" class="text-center">
                <h3>Ooops...</h3>
                <h4>Parece que no hay reservas registradas...</h4>
                <div id="bind_img_result">
                    <img src="../img/not_stonks.jpg" alt="Sin resultados" id="img_dinero_perdido" class="img-fluid">
                </div>
            </div>
        <?php else: ?>
            <div id="headerTituloFiltros">
                <h2>Histórico de Reservas Registradas</h2>
                <button class="btn btn-info btn_custom_filter" id="filter_button">Filtros</button>
            </div>
            <table class="table table-striped table-bordered mt-4" id="reservas_table">
                <thead class="table-active">
                    <tr>
                        <th scope="col">ID Reserva</th>
                        <th scope="col">Camarero</th>
                        <th scope="col">Mesa</th>
                        <th scope="col">Ubicación</th>
                        <th scope="col">Fecha de Inicio</th>
                        <th scope="col">Fecha de Finalización</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <th scope="row"><?php echo $row['id_ocupacion']; ?></th>
                            <td><?php echo htmlspecialchars($row['nombre_camarero'] . ' ' . $row['apellidos_camarero']); ?></td>
                            <td><?php echo htmlspecialchars($row['id_mesa']); ?></td>
                            <td><?php echo htmlspecialchars($row['ubicacion_sala']); ?></td>
                            <td><?php echo date("d-m-Y H:i", strtotime($row['fecha_inicio'])); ?></td>
                            <td><?php echo date("d-m-Y H:i", strtotime($row['fecha_final'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <!-- Script para Filtrar -->
    <script src="../js/filtradoHistorico.js"></script>

    <!-- Formulario para filtrar -->
    <div id="contenedorFiltros" class="container mt-4">
        <form class="form-horizontal" id="formFiltros" action="" method="POST">
            <div id="tituloFiltros">
                <h3>Filtros</h3>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="id_reserva">Id reserva:</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="id_reserva" placeholder="----------" name="id_reserva" value="<?php echo isset($_POST['id_reserva']) ? htmlspecialchars($_POST['id_reserva'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="nombre_camarero">Nombre camarero:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombre_camarero" placeholder="----------" name="nombre_camarero" value="<?php echo isset($_POST['nombre_camarero']) ? htmlspecialchars($_POST['nombre_camarero'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="apellido_camarero">Apellido camarero:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="apellido_camarero" placeholder="----------" name="apellido_camarero" value="<?php echo isset($_POST['apellido_camarero']) ? htmlspecialchars($_POST['apellido_camarero'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="id_mesa">Id mesa:</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="id_mesa" placeholder="----------" name="id_mesa" value="<?php echo isset($_POST['id_mesa']) ? htmlspecialchars($_POST['id_mesa'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="ubicacion_sala">Ubicación sala:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="ubicacion_sala" name="ubicacion_sala">
                        <option value="">----------</option>
                        <option value="Sala" <?php echo isset($_POST['ubicacion_sala']) && $_POST['ubicacion_sala'] === "Sala" ? "selected" : ""; ?>>Sala</option>
                        <option value="Terraza exterior" <?php echo isset($_POST['ubicacion_sala']) && $_POST['ubicacion_sala'] === "Terraza exterior" ? "selected" : ""; ?>>Terraza exterior</option>
                        <option value="Sala privada" <?php echo isset($_POST['ubicacion_sala']) && $_POST['ubicacion_sala'] === "Sala privada" ? "selected" : ""; ?>>Sala privada</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="fecha_inicio">Fecha de inicio:</label>
                <div class="col-sm-10">
                    <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? htmlspecialchars($_POST['fecha_inicio'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="fecha_final">Fecha final:</label>
                <div class="col-sm-10">
                    <input type="datetime-local" class="form-control" id="fecha_final" name="fecha_final" value="<?php echo isset($_POST['fecha_final']) ? htmlspecialchars($_POST['fecha_final'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="column_name">Columna:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="column_name" name="column_name">
                        <option value="">----------</option>
                        <option value="id_ocupacion" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "id_ocupacion" ? "selected" : ""; ?>>ID Reserva</option>
                        <option value="nombre_camarero" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "nombre_camarero" ? "selected" : ""; ?>>Camarero</option>
                        <option value="id_mesa" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "id_mesa" ? "selected" : ""; ?>>Mesa</option>
                        <option value="ubicacion_sala" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "ubicacion_sala" ? "selected" : ""; ?>>Ubicación</option>
                        <option value="fecha_inicio" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "fecha_inicio" ? "selected" : ""; ?>>Fecha de Inicio</option>
                        <option value="fecha_final" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "fecha_final" ? "selected" : ""; ?>>Fecha Finalización</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="ordenar_registro">Ordenar:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="ordenar_registro" name="ordenar_registro">
                        <option value="">----------</option>
                        <option value="Ascendente" <?php echo isset($_POST['ordenar_registro']) && $_POST['ordenar_registro'] === "Ascendente" ? "selected" : ""; ?>>Ascendente</option>
                        <option value="Descendente" <?php echo isset($_POST['ordenar_registro']) && $_POST['ordenar_registro'] === "Descendente" ? "selected" : ""; ?>>Descendente</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-offset-2 col-sm-10 contenedorBotonesAcciones">
                    <button type="submit" class="btn botonesAcciones btn_custom_filterOK" id="botonAplicarFiltros" name="filtrosBuscando">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>