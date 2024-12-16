<?php 
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Verificar si se recibió un ID de mesa e ID de sala
if (!isset($_GET['id_mesa']) || !isset($_GET['id_sala'])) {
    header('Location: ../view/gestionRecursos.php'); // Redirige si falta información
    exit();
}

$id_mesa = $_GET['id_mesa'];
$id_sala = $_GET['id_sala'];

// Lógica de eliminación
try {
    $conn->beginTransaction();

    // Eliminar sillas asociadas
    $stmt = $conn->prepare("DELETE FROM tbl_sillas WHERE id_mesa = ?");
    $stmt->execute([$id_mesa]);

    // Eliminar la mesa
    $stmt = $conn->prepare("DELETE FROM tbl_mesa WHERE id_mesa = ?");
    $stmt->execute([$id_mesa]);

    $conn->commit();

    // Redirección de éxito con SweetAlert
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Éxito',
                text: 'Mesa eliminada correctamente',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionMesas.php?id_sala=" . urlencode($id_sala) . "';
            });
        });
    </script>";

} catch (PDOException $e) {
    $conn->rollBack();

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error',
                text: 'Error al eliminar la mesa: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionMesas.php?id_sala=" . urlencode($id_sala) . "';
            });
        });
    </script>";
}
?>