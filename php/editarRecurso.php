<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

// Obtener el ID de la sala desde el parámetro GET
$id_sala = isset($_GET['id_sala']) ? (int)$_GET['id_sala'] : 0;

// Obtener información de la sala
$stmt = $conn->prepare("SELECT * FROM tbl_sala WHERE id_sala = ?");
$stmt->execute([$id_sala]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar que la sala existe
if (!$sala) {
    header('Location: ../view/gestionRecursos.php');
    exit();
}

// Actualizar la sala
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validar campos obligatorios
        if (empty($_POST['ubicacion'])) {
            throw new Exception('El campo ubicación es obligatorio.');
        }

        // Obtener la imagen actual
        $imagen_sala = $sala['imagen_sala']; // Mantener la imagen anterior por defecto

        // Subir nueva imagen si se proporciona
        if (!empty($_FILES['imagen']['name'])) {
            $target_dir = "../uploads/salas/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $target_file = $target_dir . basename($_FILES['imagen']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validar el formato de la imagen
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                throw new Exception('Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF).');
            }

            // Subir la nueva imagen
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                throw new Exception('Error al subir la nueva imagen.');
            }

            $imagen_sala = $target_file; // Actualizar con la nueva ruta de imagen
        }

        // Actualizar los datos de la sala
        $stmt = $conn->prepare("UPDATE tbl_sala SET ubicacion_sala = ?, imagen_sala = ? WHERE id_sala = ?");
        if ($stmt->execute([$_POST['ubicacion'], $imagen_sala, $_POST['id_sala']])) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Sala actualizada correctamente',
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
            throw new Exception('Error al actualizar la sala.');
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
    <title>Editar Sala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/historicoResponsive.css">
    <link rel="stylesheet" href="../css/crud.css">
    <style>
        .image-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #ddd;
            border-radius: 5px;
            margin-top: 10px;
            width: 200px;
            height: 150px;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
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
            <span class="span_subtitle">Editar Sala</span>
        </div>
        <nav id="nav_header">
            <a href="../view/gestionRecursos.php" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Formulario para editar la sala -->
    <main class="container mt-5">
        <h2 class="card-title mb-4">Editar Sala</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($sala['id_sala']); ?>">

            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación de la Sala</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" 
                       value="<?php echo htmlspecialchars($sala['ubicacion_sala']); ?>" required>
            </div>

            <div class="mb-3"> 
                <label for="imagen" class="form-label">Imagen Actual</label><br>
                <img src="<?php echo  htmlspecialchars($sala['imagen_sala']); ?>" alt="Imagen Actual" style="max-width: 200px; border: 1px solid #ddd; margin-bottom: 10px;">
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                <small class="text-muted">Si no seleccionas una nueva imagen, se mantendrá la actual.</small>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-danger" id="submitBtn">Actualizar Sala</button>
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
