<?php
session_start();
require_once '../conexion.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id_reserva = $_GET['id'];
    $action = $_GET['action'];
    $id_sala = $_GET['id_sala'] ?? null;

    try {
        // Primero verificamos el estado actual de la reserva
        $sql_check = "SELECT estado_reserva FROM tbl_reservas WHERE id_reserva = :id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':id', $id_reserva, PDO::PARAM_INT);
        $stmt_check->execute();
        $estado_actual = $stmt_check->fetchColumn();

        // Validamos las transiciones permitidas
        switch($action) {
            case 'cancelar':
                // Solo se puede cancelar si está Confirmada
                if ($estado_actual !== 'Confirmada') {
                    throw new Exception('Solo se pueden cancelar reservas confirmadas');
                }
                $nuevo_estado = 'Cancelada';
                break;

            case 'confirmar':
                // No debería ser necesario ya que las reservas se crean confirmadas
                if ($estado_actual !== 'Pendiente') {
                    throw new Exception('Esta reserva ya está confirmada');
                }
                $nuevo_estado = 'Confirmada';
                break;

            case 'completar':
                // Solo se puede completar si está Confirmada
                if ($estado_actual !== 'Confirmada') {
                    throw new Exception('Solo se pueden completar reservas confirmadas');
                }
                $nuevo_estado = 'Completada';
                break;

            default:
                throw new Exception('Acción no válida');
        }

        $sql = "UPDATE tbl_reservas 
                SET estado_reserva = :estado,
                    id_usuario_modificacion = :id_usuario,
                    fecha_modificacion = CURRENT_TIMESTAMP
                WHERE id_reserva = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':id', $id_reserva, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $_SESSION['user_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Reserva " . strtolower($nuevo_estado) . " correctamente";
        } else {
            throw new Exception('Error al actualizar la reserva');
        }

    } catch(Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log($e->getMessage());
    }

    header("Location: ../../view/mesasReservas.php?id_sala=" . $id_sala);
    exit();
}
?>