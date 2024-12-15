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
        // Preparamos la consulta de actualización
        $update_query_ocu = "
            UPDATE tbl_ocupacion
            SET estado_ocupacion = 'Ocupado', fecha_inicio = CURRENT_TIMESTAMP, id_usuario = :id_usuario
            WHERE id_mesa = :id_mesa AND estado_ocupacion = 'Disponible';
        ";

        // Preparamos la consulta con PDO
        $stmt_update_query_ocu = $conn->prepare($update_query_ocu);

        // Bind de los parámetros
        $stmt_update_query_ocu->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
        $stmt_update_query_ocu->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);

        // Ejecutamos la consulta
        $stmt_update_query_ocu->execute();

        // Redirigimos al final
        header("Location: ../view/mesas.php");
    } catch (PDOException $e) {
        // Mostramos un mensaje de error en caso de fallo
        echo "Error al actualizar la ocupación: " . $e->getMessage();
    }
} else {
    echo "ID de mesa no proporcionado.";
}

?>      