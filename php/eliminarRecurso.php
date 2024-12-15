<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Verificar si se recibió un ID
if (!isset($_GET['id_sala'])) {
    header('Location: ../view/gestionRecursos.php');
    exit();
}

$id_sala = $_GET['id_sala'];

try {
    $conn->beginTransaction();

    // Eliminar las sillas asociadas
    $stmt = $conn->prepare("DELETE FROM tbl_sillas WHERE id_mesa IN (SELECT id_mesa FROM tbl_mesa WHERE id_sala = ?)");
    $stmt->execute([$id_sala]);

    // Eliminar las mesas asociadas
    $stmt = $conn->prepare("DELETE FROM tbl_mesa WHERE id_sala = ?");
    $stmt->execute([$id_sala]);

    // Eliminar la sala
    $stmt = $conn->prepare("DELETE FROM tbl_sala WHERE id_sala = ?");
    $stmt->execute([$id_sala]);

    $conn->commit();

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Éxito',
                text: 'Recurso eliminado correctamente',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionRecursos.php';
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
                text: 'Error al eliminar el recurso: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionRecursos.php';
            });
        });
    </script>";
}
?>