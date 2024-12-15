<?php

// Iniciamos la sesión
session_start();

// Verificamos si la sesión del usuario está activa
if (empty($_SESSION['user_id'])) {
    // Si no está activo, redirigimos a la página de inicio de sesión
    header("Location: ./index.php");
    exit();
}

// Incluimos el archivo de conexión
require '../php/conexion.php'; 

// Verificamos si el parámetro `id` está disponible
if (isset($_GET['id'])) {
    $id_mesa = htmlspecialchars($_GET['id']);
    $user_id = $_SESSION['user_id'];

    try {
        // Iniciamos la transacción
        $conn->beginTransaction();

        // Consulta para actualizar el estado a "Registrada"
        $update_query = "
            UPDATE tbl_ocupacion
            SET estado_ocupacion = 'Registrada', fecha_final = CURRENT_TIMESTAMP, id_usuario = :id_usuario
            WHERE id_mesa = :id_mesa AND estado_ocupacion = 'Ocupado';
        ";
        $stmt_update_query = $conn->prepare($update_query);
        $stmt_update_query->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
        $stmt_update_query->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_update_query->execute();

        // Consulta para insertar un nuevo estado "Disponible"
        $insert_query = "
            INSERT INTO tbl_ocupacion (id_mesa, estado_ocupacion) 
            VALUES (:id_mesa, 'Disponible');
        ";
        $stmt_insert_query = $conn->prepare($insert_query);
        $stmt_insert_query->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt_insert_query->execute();

        // Confirmamos la transacción
        $conn->commit();

        // Redirigimos al final
        header("Location: ../view/mesas.php");
        exit();

    } catch (PDOException $e) {
        // Revertimos la transacción en caso de error
        $conn->rollBack();
        echo "Error al editar: " . $e->getMessage();
    }
} else {
    echo "ID de mesa no proporcionado.";
}

?>