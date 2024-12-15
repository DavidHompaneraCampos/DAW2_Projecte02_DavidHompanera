<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../php/cerrarSesion.php");
    exit();
}

require '../php/conexion.php';
require_once '../php/functions.php';

$id_usuario = $_SESSION['user_id'];
actualizarReservasCompletadas($conn);
$info_waiter = get_info_waiter_bbdd($conn, $id_usuario);

$conditions = "";
$order_by = "";
$params = [];

// Verificamos que use método POST y use el botón del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filtrosBuscando'])) {
    $filters = [];
    $fields = [
        'id_reserva' => ['field' => 'r.id_reserva', 'operator' => '=', 'type' => PDO::PARAM_INT],
        'nombre_camarero' => ['field' => 'c.nombre_usuario', 'operator' => 'LIKE', 'type' => PDO::PARAM_STR],
        'apellido_camarero' => ['field' => 'c.apellidos_usuario', 'operator' => 'LIKE', 'type' => PDO::PARAM_STR],
        'id_mesa' => ['field' => 'm.id_mesa', 'operator' => '=', 'type' => PDO::PARAM_INT],
        'ubicacion_sala' => ['field' => 's.ubicacion_sala', 'operator' => 'LIKE', 'type' => PDO::PARAM_STR],
        'fecha_inicio' => ['field' => 'r.fecha_reserva', 'operator' => '>=', 'type' => PDO::PARAM_STR],
        'fecha_final' => ['field' => 'r.fecha_reserva', 'operator' => '<=', 'type' => PDO::PARAM_STR]
    ];

    foreach ($fields as $post_key => $db_field) {
        if (!empty($_POST[$post_key])) {
            $filters[] = "{$db_field['field']} {$db_field['operator']} :{$post_key}";
            $params[$post_key] = [
                'value' => ($db_field['operator'] === 'LIKE') ? "%{$_POST[$post_key]}%" : $_POST[$post_key],
                'type' => $db_field['type']
            ];
        }
    }

    if (!empty($filters)) {
        $conditions = "WHERE " . implode(' AND ', $filters);
    }

    $allowed_columns = ['id_reserva', 'nombre_usuario', 'id_mesa', 'ubicacion_sala', 'fecha_reserva'];
    $ordenar_nombre_columna = $_POST['column_name'] ?? '';
    $ordenar_por = $_POST['ordenar_registro'] ?? '';

    if (in_array($ordenar_nombre_columna, $allowed_columns)) {
        $order_direction = ($ordenar_por === 'Ascendente') ? 'ASC' : 'DESC';
        $order_by = "ORDER BY {$ordenar_nombre_columna} {$order_direction}";
    }
}

$base_query = "
    SELECT 
        r.id_reserva,
        CONCAT(c.nombre_usuario, ' ', c.apellidos_usuario) as camarero,
        m.id_mesa,
        s.ubicacion_sala,
        r.fecha_reserva,
        r.estado_reserva,
        CASE 
            WHEN r.estado_reserva = 'Cancelada' AND r.id_usuario_modificacion IS NOT NULL 
            THEN CONCAT('Cancelada por ', u2.username)
            ELSE r.estado_reserva
        END as estado_detallado,
        r.fecha_modificacion
    FROM 
        tbl_reservas r
    INNER JOIN 
        tbl_usuario c ON r.id_usuario = c.id_usuario
    LEFT JOIN 
        tbl_usuario u2 ON r.id_usuario_modificacion = u2.id_usuario
    INNER JOIN 
        tbl_mesa m ON r.id_mesa = m.id_mesa
    INNER JOIN 
        tbl_sala s ON m.id_sala = s.id_sala";

$where_clause = empty($conditions) ? 
    "WHERE r.estado_reserva IN ('Confirmada', 'Cancelada', 'Completada')" : 
    $conditions . " AND r.estado_reserva IN ('Confirmada', 'Cancelada', 'Completada')";

$query = $base_query . " " . $where_clause . " " . $order_by;

$stmt = $conn->prepare($query);
foreach ($params as $key => $param) {
    $stmt->bindValue(":$key", $param['value'], $param['type']);
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/historicoResponsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Cabecera -->
    <header id="container_header">
        <!-- Contenedor del usuario -->
        <div id="container-username">
            <div id="icon_profile_header">
                <img src="../img/logoSinFondo.png" alt="" id="icon_profile">
            </div>
            <div id="username_profile_header">
                <p id="p_username_profile"><?php echo htmlspecialchars($info_waiter['username']); ?></p>
                <span class="span_subtitle">
                    <?php echo htmlspecialchars($info_waiter['name'] . " " . $info_waiter['surname']); ?>
                </span>
            </div>
        </div>

        <!-- Contenedor del título -->
        <div id="container_title_header">
            <h1 id="title_header"><strong>Dinner At Westfield</strong></h1>
            <span class="span_subtitle">Gestión de Reservas</span>
        </div>

        <!-- Navegación -->
        <nav id="nav_header">
            <a href="./reservas.php" class="btn btn-danger me-2 btn_custom_logOut">Reservas</a>
            <a href="./mesas.php" class="btn btn-danger me-2 btn_custom_logOut">Ocupaciones</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <main class="container mt-5">
        <div id="headerTituloFiltros" class="d-flex justify-content-between align-items-center">
            <h2>Histórico de Reservas</h2>
            <div>
                <button class="btn btn-danger btn_custom_filter me-2" id="filter_button">Filtros</button>
                <a href="historicoReservas.php" class="btn btn-secondary">Eliminar Filtros</a>
            </div>
        </div>

        <table class="table table-striped table-bordered mt-4">
            <thead class="table-active">
                <tr>
                    <th>ID Reserva</th>
                    <th>Camarero</th>
                    <th>Mesa</th>
                    <th>Ubicación</th>
                    <th>Fecha Reserva</th>
                    <th>Estado</th>
                    <th>Fecha Modificación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($result)): ?>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_reserva'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['camarero'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['id_mesa'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['ubicacion_sala'] ?? ''); ?></td>
                            <td><?php echo $row['fecha_reserva'] ? date("d-m-Y H:i", strtotime($row['fecha_reserva'])) : ''; ?></td>
                            <td><?php echo htmlspecialchars($row['estado_detallado'] ?? ''); ?></td>
                            <td><?php echo $row['fecha_modificacion'] ? date("d-m-Y H:i", strtotime($row['fecha_modificacion'])) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Contenedor de filtros -->
    <div id="contenedorFiltros" class="container mt-4">
        <form class="form-horizontal" id="formFiltros" action="" method="POST">
            <div id="tituloFiltros">
                <h3>Filtros</h3>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="id_reserva">Id reserva:</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="id_reserva" placeholder="----------" name="id_reserva" 
                           value="<?php echo isset($_POST['id_reserva']) ? htmlspecialchars($_POST['id_reserva'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="nombre_camarero">Nombre camarero:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombre_camarero" placeholder="----------" name="nombre_camarero" 
                           value="<?php echo isset($_POST['nombre_camarero']) ? htmlspecialchars($_POST['nombre_camarero'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="apellido_camarero">Apellido camarero:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="apellido_camarero" placeholder="----------" name="apellido_camarero" 
                           value="<?php echo isset($_POST['apellido_camarero']) ? htmlspecialchars($_POST['apellido_camarero'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="id_mesa">Id mesa:</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="id_mesa" placeholder="----------" name="id_mesa" 
                           value="<?php echo isset($_POST['id_mesa']) ? htmlspecialchars($_POST['id_mesa'], ENT_QUOTES, 'UTF-8') : ''; ?>">
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
                    <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?php echo isset($_POST['fecha_inicio']) ? htmlspecialchars($_POST['fecha_inicio'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="fecha_final">Fecha final:</label>
                <div class="col-sm-10">
                    <input type="datetime-local" class="form-control" id="fecha_final" name="fecha_final" 
                           value="<?php echo isset($_POST['fecha_final']) ? htmlspecialchars($_POST['fecha_final'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-sm-2" for="column_name">Columna:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="column_name" name="column_name">
                        <option value="">----------</option>
                        <option value="id_reserva" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "id_reserva" ? "selected" : ""; ?>>ID Reserva</option>
                        <option value="camarero" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "camarero" ? "selected" : ""; ?>>Camarero</option>
                        <option value="id_mesa" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "id_mesa" ? "selected" : ""; ?>>Mesa</option>
                        <option value="ubicacion_sala" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "ubicacion_sala" ? "selected" : ""; ?>>Ubicación</option>
                        <option value="fecha_reserva" <?php echo isset($_POST['column_name']) && $_POST['column_name'] === "fecha_reserva" ? "selected" : ""; ?>>Fecha Reserva</option>
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

    <script src="../js/filtradoHistorico.js"></script>
</body>
</html>