<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

if (!isset($_GET['id_mesa']) || !isset($_GET['id_sala'])) {
    header('Location: ../view/gestionRecursos.php');
    exit();
}

$id_mesa = (int)$_GET['id_mesa'];
$id_sala = (int)$_GET['id_sala'];

// Opcional: actualizar la sesión con el id_sala
$_SESSION['id_sala'] = $id_sala;

// Recuperar los datos actuales de la mesa
try {
    $stmt = $conn->prepare("SELECT numero_sillas_mesa FROM tbl_mesa WHERE id_mesa = ? AND id_sala = ?");
    $stmt->execute([$id_mesa, $id_sala]);
    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mesa) {
        throw new Exception('La mesa seleccionada no existe o no pertenece a la sala.');
    }
} catch (Exception $e) {
    header('Location: ../view/gestionMesas.php?id_sala=' . $id_sala);
    exit();
}

// Procesar el formulario de edición
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

        // Actualizar la mesa en la base de datos
        $stmt = $conn->prepare("UPDATE tbl_mesa SET numero_sillas_mesa = ? WHERE id_mesa = ? AND id_sala = ?");
        if ($stmt->execute([$numero_sillas, $id_mesa, $id_sala])) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Mesa actualizada correctamente',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../view/gestionMesas.php?id_sala=$id_sala';
                        }
                    });
                });
            </script>";
            exit();
        } else {
            throw new Exception('Error al actualizar la mesa.');
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
    <title>Editar Mesa</title>
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
            <span class="span_subtitle">Editar Mesa</span>
        </div>
        <nav id="nav_header">
            <a href="../view/gestionMesas.php?id_sala=<?php echo $id_sala; ?>" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Formulario para editar mesas -->
    <main class="container mt-5">
        <h2 class="card-title mb-4">Editar Mesa</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="numero_sillas" class="form-label" style="color: #333333 !important;">Número de Sillas</label>
                <input type="number" class="form-control" id="numero_sillas" name="numero_sillas" 
                       value="<?php echo htmlspecialchars($mesa['numero_sillas_mesa']); ?>" 
                       style="background-color: white !important; border: none !important; color: #333333 !important;" 
                       required>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-danger" id="submitBtn">Guardar Cambios</button>
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