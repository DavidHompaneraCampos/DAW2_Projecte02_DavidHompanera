<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

// Verificar si se recibió un ID de mesa
if (!isset($_GET['id_mesa'])) {
    header('Location: ../view/gestionMesas.php');
    exit();
}

$id_mesa = $_GET['id_mesa'];

try {
    $conn->beginTransaction();

    // Eliminar las sillas asociadas a la mesa
    $stmt = $conn->prepare("DELETE FROM tbl_sillas WHERE id_mesa = ?");
    $stmt->execute([$id_mesa]);

    // Eliminar la mesa
    $stmt = $conn->prepare("DELETE FROM tbl_mesa WHERE id_mesa = ?");
    $stmt->execute([$id_mesa]);

    $conn->commit();

    // Mostrar mensaje de éxito con SweetAlert2
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Éxito',
                text: 'Mesa eliminada correctamente',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionMesas.php?id_sala=" . $_GET['id_sala'] . "';
            });
        });
    </script>";

} catch (PDOException $e) {
    $conn->rollBack();

    // Mostrar mensaje de error con SweetAlert2
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error',
                text: 'Error al eliminar la mesa: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../view/gestionMesas.php?id_sala=" . $_GET['id_sala'] . "';
            });
        });
    </script>";
}
?>