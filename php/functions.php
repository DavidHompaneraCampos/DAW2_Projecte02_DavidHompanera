<?php
function redirect_with_errors($url, $errors) {
    if (!empty($errors) && !empty($url)) {
        foreach ($errors as $valor) {
            $erroresPrepared['error'] = $valor;
        }
        $errorParams = http_build_query($erroresPrepared);
        header("Location: {$url}?{$errorParams}");
        exit();
    }
}

// Función que recupera la información del camarero
function get_info_waiter_bbdd($conn, $id_camarero) {
    try {
        // Preparamos la consulta
        $query = "SELECT * FROM tbl_usuario WHERE id_usuario = :id_camarero";
        $stmt_info = $conn->prepare($query);

        // Ejecutamos la consulta con el parámetro
        $stmt_info->bindParam(':id_camarero', $id_camarero, PDO::PARAM_INT);
        $stmt_info->execute();

        // Obtenemos el resultado
        $row = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'username' => $row['username'], 
                'name' => $row['nombre_usuario'], 
                'surname' => $row['apellidos_usuario']
            ];
        } else {
            return null; // No se encontró el camarero
        }
    } catch (PDOException $e) {
        error_log("Error al recuperar información del camarero: " . $e->getMessage());
        return null;
    }
}

// Función simple para verificar el estado de una reserva
function get_estado_reserva($conn, $id_reserva) {
    try {
        $query = "SELECT estado_reserva FROM tbl_reservas WHERE id_reserva = :id_reserva";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error al obtener estado de reserva: " . $e->getMessage());
        return null;
    }
}

/**
 * Actualiza el estado de las reservas a 'Completada' cuando su fecha ha pasado
 * @param PDO $conn Conexión a la base de datos
 * @return bool True si la actualización fue exitosa, False en caso contrario
 */
function actualizarReservasCompletadas($conn) {
    try {
        $query = "UPDATE tbl_reservas 
                  SET estado_reserva = 'Completada'
                  WHERE fecha_reserva < DATE_SUB(NOW(), INTERVAL 2 HOUR)
                  AND estado_reserva = 'Confirmada'";
        
        $stmt = $conn->prepare($query);
        $resultado = $stmt->execute();
        
        if($resultado) {
            error_log("Actualización de estados completada: " . date('Y-m-d H:i:s'));
            return true;
        } else {
            error_log("Error en actualización de estados: " . date('Y-m-d H:i:s'));
            return false;
        }
    } catch (PDOException $e) {
        error_log("Error en la actualización: " . $e->getMessage());
        return false;
    }
}
?>