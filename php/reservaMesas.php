<?php

// Iniciamos la sesión
session_start();

// Verificamos si la sesión del camarero está activa
if (empty($_SESSION['user_id'])) {
    // Si no está activo, redirigimos a la página de inicio de sesión
    header("Location: ./index.php");
    exit();
}

require '../php/conexion.php'; 

if (isset($_GET['id'])) {
    $id_mesa = htmlspecialchars($_GET['id']);
    $user_id =$_SESSION['user_id'];

    $update_query_ocu = "
        UPDATE tbl_ocupacion
        SET estado_ocupacion = 'Ocupado', fecha_inicio = CURRENT_TIMESTAMP, id_camarero = ?
        WHERE id_mesa = ? AND estado_ocupacion = 'Disponible';
    ";


    $stmt_update_query_ocu = mysqli_prepare($conn, $update_query_ocu);
    mysqli_stmt_bind_param($stmt_update_query_ocu, "ii", $user_id, $id_mesa);
    mysqli_stmt_execute($stmt_update_query_ocu);
    mysqli_stmt_close($stmt_update_query_ocu);

    // Redirigir al final
    header("Location: ../view/mesas.php");
} else {
    echo "ID de mesa no proporcionado.";
}

?>
