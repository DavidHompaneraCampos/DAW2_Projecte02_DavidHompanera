<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Validar si existe un id_sala en la sesión
if (!isset($_SESSION['id_sala']) || $_SESSION['id_sala'] === 0) {
    if (isset($_GET['id_sala'])) {
        $_SESSION['id_sala'] = (int)$_GET['id_sala'];
    } else {
        header('Location: ../view/gestionRecursos.php');
        exit();
    }
}
$id_sala = $_SESSION['id_sala'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validar campos obligatorios
        if (empty($_POST['numero_sillas'])) {
            throw new Exception('El número de sillas es obligatorio.');
        }

        // Validar que sea un entero positivo
        $numero_sillas = filter_var($_POST['numero_sillas'], FILTER_VALIDATE_INT);

        if ($numero_sillas === false || $numero_sillas <= 0) {
            throw new Exception('El número de sillas debe ser un número positivo.');
        }

        if ($numero_sillas > 30) {
            throw new Exception('El número de sillas no puede ser mayor a 30.');
        }

        // Insertar la nueva mesa en la base de datos
        $stmt = $conn->prepare("INSERT INTO tbl_mesa (id_sala, numero_sillas_mesa) VALUES (?, ?)");
        if ($stmt->execute([$id_sala, $numero_sillas])) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Mesa creada correctamente',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../view/gestionMesas.php';
                        }
                    });
                });
            </script>";
            exit();
        } else {
            throw new Exception('Error al crear la mesa.');
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
    <title>Crear Mesa</title>
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
            <span class="span_subtitle">Crear Mesa</span>
        </div>
        <nav id="nav_header">
            <a href="../view/gestionMesas.php" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Formulario para crear mesas -->
    <main class="container mt-5">
        <h2 class="card-title mb-4">Crear Nueva Mesa</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="numero_sillas" class="form-label" style="color: #333333 !important;">Número de Sillas</label>
                <input type="number" class="form-control" id="numero_sillas" name="numero_sillas" style="background-color: white !important; border: none !important; color: #333333 !important;" required>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-danger" id="submitBtn">Crear Mesa</button>
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