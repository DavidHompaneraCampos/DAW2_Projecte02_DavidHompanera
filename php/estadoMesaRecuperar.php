<?php
require '../php/conexion.php'; 

try {
    // Preparamos y ejecutamos la consulta
    $sqlRecuperarEstados = "SELECT id_mesa, estado_ocupacion FROM tbl_ocupacion WHERE estado_ocupacion NOT LIKE 'Registrada'";
    $stmt_table_state = $conn->prepare($sqlRecuperarEstados);
    $stmt_table_state->execute();

    // Obtenemos los resultados
    $resultado = $stmt_table_state->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($resultado)) {
        // Creamos un array para almacenar las ocupaciones
        $ARRAYocupaciones = [];
        foreach ($resultado as $fila) {
            $ARRAYocupaciones[$fila['id_mesa']] = $fila['estado_ocupacion'];
        }

        // Verificamos si la sesión ya está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Guardamos el array en la sesión
        $_SESSION['ARRAYocupaciones'] = $ARRAYocupaciones;
    }
} catch (PDOException $e) {
    // Puedes registrar el error en un log si lo deseas
    error_log("Error al recuperar los datos: " . $e->getMessage());
    // No mostramos nada al usuario
}
?>