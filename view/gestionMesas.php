<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Obtener el ID de la sala seleccionada
if (isset($_GET['id_sala'])) {
    $_SESSION['id_sala'] = (int)$_GET['id_sala']; // Guardar el id_sala en la sesión
}

// Validar si existe un id_sala en la sesión
if (!isset($_SESSION['id_sala']) || $_SESSION['id_sala'] === 0) {
    header('Location: gestionRecursos.php'); // Redirigir si no hay sala válida
    exit();
}

$id_sala = $_SESSION['id_sala'];

// Obtener la información de la sala
$stmt_sala = $conn->prepare("SELECT ubicacion_sala FROM tbl_sala WHERE id_sala = ?");
$stmt_sala->execute([$id_sala]);
$sala = $stmt_sala->fetch(PDO::FETCH_ASSOC);

// Validar si la sala existe
if (!$sala) {
    unset($_SESSION['id_sala']); // Limpiar sesión si la sala no existe
    echo "<script>
            alert('La sala seleccionada no existe.');
            window.location.href='gestionRecursos.php';
          </script>";
    exit();
}

// Obtener las mesas de la sala seleccionada
$query = "
    SELECT 
        m.id_mesa,
        m.numero_sillas_mesa
    FROM tbl_mesa m
    WHERE m.id_sala = ?
    ORDER BY m.id_mesa";

$stmt = $conn->prepare($query);
$stmt->execute([$id_sala]);
$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Cabecera -->
    <header id="container_header">
        <div id="container-username">
            <div id="icon_profile_header">
                <img src="../img/logoSinFondo.png" alt="" id="icon_profile">
            </div>
            <div id="username_profile_header">
                <p id="p_username_profile">admin</p>
                <span class="span_subtitle">Admin Principal</span>
            </div>
        </div>
        <div id="container_title_header">
            <h1 id="title_header"><strong>Dinner At Westfield</strong></h1>
            <span class="span_subtitle">
                Gestión de Mesas - <?php echo htmlspecialchars($sala['ubicacion_sala']); ?>
            </span>
        </div>
        <nav id="nav_header">
            <a href="gestionRecursos.php" class="btn btn-danger btn_custom_logOut m-1" 
               onclick="unsetSession();">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Tabla de gestión de mesas -->
    <main class="container mt-5">
        <div id="headerTituloFiltros" class="d-flex justify-content-between align-items-center">
            <h2>Gestión de Mesas</h2>
            <a href="../php/crearRecurso.php?id_sala=<?php echo $id_sala; ?>" 
               class="btn btn-danger btn_custom_filter me-2">
                <i class="fas fa-plus"></i> Nueva Mesa
            </a>
        </div>

        <table class="table table-striped table-bordered mt-4">
            <thead class="table-active">
                <tr>
                    <th>Número de Mesa</th>
                    <th>Número de Sillas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($mesas)): ?>
                    <?php foreach ($mesas as $mesa): ?>
                        <tr onclick="window.location.href='gestionSillas.php?id_mesa=<?php echo $mesa['id_mesa']; ?>&id_sala=<?php echo $id_sala; ?>'"                        style="background-color: rgba(0, 0, 0, 0.1); cursor: pointer;"
                        onmouseover="this.style.backgroundColor='rgba(0, 0, 0, 0.2)';" 
                        onmouseout="this.style.backgroundColor='rgba(0, 0, 0, 0.1)';">
                            <td><?php echo htmlspecialchars($mesa['id_mesa']); ?></td>
                            <td><?php echo htmlspecialchars($mesa['numero_sillas_mesa']); ?></td>
                            <td>
                                <a href="editarMesa.php?id_mesa=<?php echo $mesa['id_mesa']; ?>" 
                                   class="btn btn-warning btn-sm" onclick="event.stopPropagation();">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="event.stopPropagation(); confirmarEliminar(<?php echo $mesa['id_mesa']; ?>)" 
                                        class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay mesas disponibles en esta sala.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Bootstrap JS y SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmarEliminar(id_mesa) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'No podrás revertir esta acción',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `../php/eliminarMesa.php?id_mesa=${id_mesa}`;
            }
        });
    }
    function unsetSession() {
        <?php unset($_SESSION['id_sala']); ?>
    }
    </script>
</body>
</html>