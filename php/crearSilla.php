<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Validar si existe un id_mesa en la URL y en la sesión
if (!isset($_GET['id_mesa'])) {
    header('Location: ../view/gestionRecursos.php');
    exit();
}

$id_mesa = (int)$_GET['id_mesa'];

// Verificar si la mesa existe
try {
    $stmt = $conn->prepare("SELECT id_mesa FROM tbl_mesa WHERE id_mesa = ?");
    $stmt->execute([$id_mesa]);
    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mesa) {
        throw new Exception('La mesa seleccionada no existe.');
    }
} catch (Exception $e) {
    header('Location: ../view/gestionRecursos.php');
    exit();
}

// Procesar el formulario para crear una nueva silla
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validar campos obligatorios
        if (empty($_POST['estado_silla'])) {
            throw new Exception('El estado de la silla es obligatorio.');
        }

        // Validar el estado de la silla
        $estado_silla = $_POST['estado_silla'];
        if (!in_array($estado_silla, ['disponible', 'rota'])) {
            throw new Exception('El estado de la silla debe ser "disponible" o "rota".');
        }

        // Insertar la nueva silla en la base de datos
        $stmt = $conn->prepare("INSERT INTO tbl_sillas (id_mesa, estado_silla) VALUES (?, ?)");
        if ($stmt->execute([$id_mesa, $estado_silla])) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Silla creada correctamente',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../view/gestionSillas.php?id_mesa=$id_mesa';
                        }
                    });
                });
            </script>";
            exit();
        } else {
            throw new Exception('Error al crear la silla.');
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
    <title>Crear Silla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
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
            <span class="span_subtitle">Crear Silla</span>
        </div>
        <nav id="nav_header">
            <a href="../view/gestionSillas.php?id_mesa=<?php echo $id_mesa; ?>" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Formulario para crear sillas -->
    <main class="container mt-5">
        <h2 class="card-title mb-4">Crear Nueva Silla</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="estado_silla" class="form-label" style="color: #333333 !important;">Estado de la Silla</label>
                <select class="form-control" id="estado_silla" name="estado_silla" style="background-color: white !important; border: none !important; color: #333333 !important;" required>
                    <option value="" disabled selected>Selecciona el estado</option>
                    <option value="disponible">Disponible</option>
                    <option value="rota">Rota</option>
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-danger" id="submitBtn">Crear Silla</button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($error)): ?>
        <script>
            Swal.fire({
                title: 'Error',
                text: '<?php echo htmlspecialchars($error); ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
</body>
</html>