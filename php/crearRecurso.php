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
        if (empty($_POST['ubicacion']) || empty($_FILES['imagen']['name'])) {
            throw new Exception('Todos los campos son obligatorios');
        }

        // Validar imagen
        $target_dir = "../uploads/salas/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $target_file = $target_dir . basename($_FILES['imagen']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar formato de imagen
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            throw new Exception('Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF).');
        }

        // Subir la imagen
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            throw new Exception('Error al subir la imagen.');
        }

        // Insertar la nueva sala en la base de datos
        $stmt = $conn->prepare("INSERT INTO tbl_sala (ubicacion_sala, imagen_sala) VALUES (?, ?)");
        if ($stmt->execute([
            $_POST['ubicacion'],
            $target_file
        ])) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Sala creada correctamente',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../view/gestionRecursos.php';
                        }
                    });
                });
            </script>";
            exit();
        } else {
            throw new Exception('Error al crear la sala.');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Sala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/historicoResponsive.css">
    <link rel="stylesheet" href="../css/crud.css">
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
            <span class="span_subtitle">Crear Sala</span>
        </div>
        <nav id="nav_header">
            <a href="../view/gestionRecursos.php" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Formulario para crear salas -->
    <main class="container mt-5">
        <h2 class="card-title mb-4">Crear Nueva Sala</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación de la Sala</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen de la Sala</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-danger" id="submitBtn">Crear Sala</button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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