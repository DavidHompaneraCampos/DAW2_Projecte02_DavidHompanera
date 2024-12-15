<?php
session_start();
require_once '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_SESSION['user_id'])) {
        header("Location: ../../view/index.php");
        exit();
    }

    $id_mesa = $_POST['id_mesa'] ?? null;
    $fecha = $_POST['fecha_reserva'] ?? null;
    $hora = $_POST['hora_reserva'] ?? null;
    $id_usuario = $_SESSION['user_id'];
    $id_sala = $_POST['id_sala'] ?? null;

    if (!$id_mesa || !$fecha || !$hora || !$id_sala) {
        $_SESSION['error'] = "Todos los campos son obligatorios";
        header("Location: ../../view/mesasReservas.php?id_sala=" . $id_sala);
        exit();
    }

    // Validar hora de reserva (13:00-23:00)
    if ($hora < '13:00' || $hora > '23:00') {
        $_SESSION['error'] = "Solo se permiten reservas entre las 13:00 y las 23:00";
        header("Location: ../../view/mesasReservas.php?id_sala=" . $id_sala);
        exit();
    }

    $fecha_hora_reserva = $fecha . ' ' . $hora;

    // Validar que la fecha no sea pasada
    if (strtotime($fecha_hora_reserva) < time()) {
        $_SESSION['error'] = "No se pueden hacer reservas para fechas pasadas";
        header("Location: ../../view/mesasReservas.php?id_sala=" . $id_sala);
        exit();
    }

    try {
        // Comprobar reservas existentes para esa mesa en ese día
        $sql_check = "SELECT fecha_reserva 
                      FROM tbl_reservas 
                      WHERE id_mesa = :id_mesa 
                      AND DATE(fecha_reserva) = DATE(:fecha_hora)
                      AND estado_reserva = 'Confirmada'";  // Solo verificamos las confirmadas
        
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':id_mesa', $id_mesa);
        $stmt_check->bindParam(':fecha_hora', $fecha_hora_reserva);
        $stmt_check->execute();
        $reservas_existentes = $stmt_check->fetchAll(PDO::FETCH_ASSOC);

        // Verificar el tiempo entre reservas (mínimo 2 horas)
        foreach ($reservas_existentes as $reserva) {
            $tiempo_entre_reservas = abs(strtotime($fecha_hora_reserva) - strtotime($reserva['fecha_reserva']));
            $horas_entre_reservas = $tiempo_entre_reservas / 3600;
            
            if ($horas_entre_reservas < 2) {
                $_SESSION['error'] = "Ya existe una reserva para las " . date('H:i', strtotime($reserva['fecha_reserva'])) . 
                                   ". Debe haber al menos 2 horas entre reservas.";
                header("Location: ../../view/mesasReservas.php?id_sala=" . $id_sala);
                exit();
            }
        }

        // Si pasa todas las validaciones, crear la reserva como confirmada
        $sql = "INSERT INTO tbl_reservas (id_mesa, id_usuario, fecha_reserva, estado_reserva) 
                VALUES (:id_mesa, :id_usuario, :fecha_hora, 'Confirmada')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $fecha_hora_reserva);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Reserva creada correctamente.";  // Eliminado "Pendiente de confirmación"
        } else {
            $_SESSION['error'] = "Error al crear la reserva";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error al crear la reserva";
        error_log($e->getMessage());
    }

    header("Location: ../../view/mesasReservas.php?id_sala=" . $id_sala);
    exit();
}
?>