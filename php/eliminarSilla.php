<?php  
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Verificar si se recibió un ID de silla e ID de mesa
if (!isset($_GET['id_silla']) || !isset($_GET['id_mesa'])) {
    header('Location: ../view/gestionRecursos.php'); // Redirige si falta información
    exit();
}

$id_silla = (int)$_GET['id_silla'];
$id_mesa = (int)$_GET['id_mesa'];

try {
    $conn->beginTransaction();

    // Eliminar la silla específica
    $stmt = $conn->prepare("DELETE FROM tbl_sillas WHERE id_silla = ?");
    $stmt->execute([$id_silla]);

    $conn->commit();

    // Redirección de éxito con SweetAlert
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Éxito',
                text: 'Silla eliminada correctamente',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionSillas.php?id_mesa=" . urlencode($id_mesa) . "';
            });
        });
    </script>";

} catch (PDOException $e) {
    $conn->rollBack();

    // Mostrar error con SweetAlert
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error',
                text: 'Error al eliminar la silla: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionSillas.php?id_mesa=" . urlencode($id_mesa) . "';
            });
        });
    </script>";
}
?>