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

    try{
        mysqli_autocommit($conn, false);
    
        mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
    
        $update_query = "UPDATE tbl_ocupacion
                         SET estado_ocupacion = 'Registrada', fecha_final = CURRENT_TIMESTAMP, id_camarero = ?
                         WHERE id_mesa = ? AND estado_ocupacion = 'Ocupado';
                         ";
        $stmt_update_query_lib = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt_update_query_lib, "ii", $_SESSION['user_id'], $id_mesa,);
        mysqli_stmt_execute($stmt_update_query_lib);
        mysqli_stmt_close($stmt_update_query_lib);
        

        $insert_query = "INSERT INTO tbl_ocupacion (id_mesa, estado_ocupacion) 
                         VALUES (?, 'Disponible');
                         ";
        
        $stmt_insert_query_reg = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert_query_reg, "i", $id_mesa);
        mysqli_stmt_execute($stmt_insert_query_reg); 
        mysqli_stmt_close($stmt_insert_query_reg);
        
        mysqli_commit($conn);
    
        // Redirigir al final
        header("Location: ../view/mesas.php");
        exit();
    
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error al editar: " . $e->getMessage();
        }

} else {
    echo "ID de mesa no proporcionado.";
}

?>
