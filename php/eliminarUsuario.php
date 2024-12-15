<?php
session_start();
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminando Usuario</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

// Verificar si se recibió un ID
if (!isset($_GET['id'])) {
    header('Location: ../view/gestionUsuarios.php');
    exit();
}

$id_usuario = $_GET['id'];

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Verificar que no sea el último administrador
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_usuario WHERE id_rol = 1");
    $stmt->execute();
    $total_admins = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT id_rol FROM tbl_usuario WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $user_rol = $stmt->fetchColumn();

    if ($user_rol == 1 && $total_admins <= 1) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'No se puede eliminar el último administrador del sistema',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionUsuarios.php';
            });
        </script>";
        exit();
    }

    // Actualizar las reservas relacionadas
    $stmt = $conn->prepare("UPDATE tbl_reservas SET id_usuario_modificacion = NULL WHERE id_usuario_modificacion = ?");
    $stmt->execute([$id_usuario]);

    // Actualizar las ocupaciones relacionadas
    $stmt = $conn->prepare("UPDATE tbl_ocupacion SET id_usuario = NULL WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);

    // Eliminar el usuario
    $stmt = $conn->prepare("DELETE FROM tbl_usuario WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);

    // Confirmar transacción
    $conn->commit();

    echo "<script>
        Swal.fire({
            title: 'Éxito',
            text: 'Usuario eliminado correctamente',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = '../view/gestionUsuarios.php';
        });
    </script>";

} catch (PDOException $e) {
    // Revertir transacción en caso de error
    $conn->rollBack();
    
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Error al eliminar el usuario: " . $e->getMessage() . "',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = '../view/gestionUsuarios.php';
        });
    </script>";
}
?>
</body>
</html>