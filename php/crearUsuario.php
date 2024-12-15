<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validar campos obligatorios
        if (empty($_POST['username']) || empty($_POST['password']) || 
            empty($_POST['password2']) || empty($_POST['nombre']) || 
            empty($_POST['apellidos']) || empty($_POST['rol'])) {
            throw new Exception('Todos los campos son obligatorios');
        }

        // Verificar que las contraseñas coinciden
        if ($_POST['password'] !== $_POST['password2']) {
            throw new Exception('Las contraseñas no coinciden');
        }

        // Verificar si el username ya existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_usuario WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('El nombre de usuario ya existe');
        }

        // Hash de la contraseña
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO tbl_usuario (username, password, nombre_usuario, 
                                                       apellidos_usuario, id_rol) 
                              VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([
            $_POST['username'],
            $password_hash,
            $_POST['nombre'],
            $_POST['apellidos'],
            $_POST['rol']
        ])) {
            $_SESSION['success_message'] = true;
            ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Usuario creado correctamente',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../view/gestionUsuarios.php';
                        }
                    });
                });
            </script>
            <?php
            exit();
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Obtener roles disponibles
$stmt = $conn->query("SELECT id_rol, nombre_rol FROM tbl_roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/historicoResponsive.css">
    <link rel="stylesheet" href="../css/crud.css">
    <script src="../js/valCrearUsuario.js" defer></script>
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
            <span class="span_subtitle">Crear Usuario</span>
        </div>

        <!-- Navegación -->
        <nav id="nav_header">
            <a href="../view/gestionUsuarios.php" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut">Cerrar sesión</a>
        </nav>
    </header>

    <main class="container mt-5">
                <h2 class="card-title mb-4">Crear Nuevo Usuario</h2>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="username" name="username">
                        <div id="errorUsername" class="error-message"></div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                        <div id="errorNombre" class="error-message"></div>
                    </div>

                    <div class="mb-3">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos">
                        <div id="errorApellidos" class="error-message"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div id="errorPassword" class="error-message"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password2" class="form-label">Repetir Contraseña</label>
                        <input type="password" class="form-control" id="password2" name="password2">
                        <div id="errorPassword2" class="error-message"></div>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-control" id="rol" name="rol">
                            <option value="" disabled selected>Seleccione un rol</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['id_rol']; ?>">
                                    <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="errorRol" class="error-message"></div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger" id="submitBtn">Crear Usuario</button>
                    </div>
                </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($error)): ?>
        <script>
            Swal.fire({
                title: 'Error',
                text: '<?php echo $error; ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
</body>
</html>
