<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

// Obtener lista de usuarios
$query = "SELECT u.id_usuario, u.username, u.nombre_usuario, u.apellidos_usuario, 
                 r.nombre_rol
          FROM tbl_usuario u
          INNER JOIN tbl_roles r ON u.id_rol = r.id_rol 
          ORDER BY u.id_usuario";
$stmt = $conn->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
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
                <p id="p_username_profile">admin</p>
                <span class="span_subtitle">Admin Principal</span>
            </div>
        </div>

        <!-- Contenedor del título -->
        <div id="container_title_header">
            <h1 id="title_header"><strong>Dinner At Westfield</strong></h1>
            <span class="span_subtitle">Gestión de Usuarios</span>
        </div>

        <!-- Navegación -->
        <nav id="nav_header">
            <a href="./reservas.php" class="btn btn-danger me-2 btn_custom_logOut">Reservas</a>
            <a href="./mesas.php" class="btn btn-danger me-2 btn_custom_logOut">Ocupaciones</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <main class="container mt-5">
        <div id="headerTituloFiltros" class="d-flex justify-content-between align-items-center">
            <h2>Gestión de Usuarios</h2>
            <div>
                <a href="../php/crearUsuario.php" class="btn btn-danger btn_custom_filter me-2">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>

        <table class="table table-striped table-bordered mt-4">
            <thead class="table-active">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['apellidos_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                    <td>
                        <a href="../php/editarUsuario.php?id=<?php echo $usuario['id_usuario']; ?>" 
                           class="btn btn-warning btn-sm">
                           <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmarEliminar(<?php echo $usuario['id_usuario']; ?>)" 
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `../php/eliminarUsuario.php?id=${id}`;
            }
        })
    }
    </script>
</body>
</html>