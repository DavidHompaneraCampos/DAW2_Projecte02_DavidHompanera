<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Obtener el ID de la mesa seleccionada
if (isset($_GET['id_mesa'])) {
    $_SESSION['id_mesa'] = (int)$_GET['id_mesa'];
}
if (isset($_GET['id_sala'])) {
    $_SESSION['id_sala'] = (int)$_GET['id_sala'];
}

// Validar la sesión de id_mesa e id_sala
if (!isset($_SESSION['id_mesa']) || $_SESSION['id_mesa'] === 0) {
    header('Location: gestionMesas.php');
    exit();
}

$id_mesa = $_SESSION['id_mesa'];
$id_sala = $_SESSION['id_sala'] ?? 0;

// Obtener las sillas de la mesa seleccionada
$query = "SELECT id_silla, estado_silla FROM tbl_sillas WHERE id_mesa = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_mesa]);
$sillas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sillas</title>
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
            <span class="span_subtitle">Gestión de Sillas - Mesa <?php echo htmlspecialchars($id_mesa); ?></span>
        </div>
        <nav id="nav_header">
            <a href="gestionMesas.php?id_sala=<?php echo $id_sala; ?>" class="btn btn-danger btn_custom_logOut m-1">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Tabla de gestión de sillas -->
    <main class="container mt-5">
        <div id="headerTituloFiltros" class="d-flex justify-content-between align-items-center">
            <h2>Gestión de Sillas</h2>
            <a href="../php/crearSilla.php?id_mesa=<?php echo $id_mesa; ?>" class="btn btn-danger btn_custom_filter me-2">
                <i class="fas fa-plus"></i> Nueva Silla
            </a>
        </div>

        <table class="table table-striped table-bordered mt-4">
            <thead class="table-active">
                <tr>
                    <th>ID Silla</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sillas)): ?>
                    <?php foreach ($sillas as $silla): ?>
                        <tr style="background-color: rgba(0, 0, 0, 0.1); cursor: pointer;"
                            onmouseover="this.style.backgroundColor='rgba(0, 0, 0, 0.2)';" 
                            onmouseout="this.style.backgroundColor='rgba(0, 0, 0, 0.1)';">
                            <td><?php echo htmlspecialchars($silla['id_silla']); ?></td>
                            <td><?php echo htmlspecialchars($silla['estado_silla']); ?></td>
                            <td>
                                <a href="../php/editarSilla.php?id_silla=<?php echo $silla['id_silla']; ?>&id_mesa=<?php echo $id_mesa; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmarEliminar(<?php echo $silla['id_silla']; ?>, <?php echo $id_mesa; ?>)" 
                                        class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay sillas disponibles en esta mesa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Bootstrap JS y SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmarEliminar(id_silla, id_mesa) {
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
                window.location.href = `../php/eliminarSilla.php?id_silla=${id_silla}&id_mesa=${id_mesa}`;
            }
        });
    }
    </script>
</body>
</html>
