<?php
require '../php/conexion.php'; 
try {
    $sqlRecuperarEstados = "SELECT id_mesa, estado_ocupacion FROM tbl_ocupacion WHERE estado_ocupacion NOT LIKE 'Registrada';";

    $stmt_table_state = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt_table_state, $sqlRecuperarEstados);
    mysqli_stmt_execute($stmt_table_state);
    $resultado = mysqli_stmt_get_result($stmt_table_state);

    $rows = mysqli_num_rows($resultado);
    
    if($rows === 0) {
        echo "No se ha encontrado ninguna mesa.";
    } else {
        $ARRAYocupaciones = array();
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $ARRAYocupaciones[$fila['id_mesa']] = $fila['estado_ocupacion'];
        }
        $_SESSION['ARRAYocupaciones'] = $ARRAYocupaciones;
    }
    mysqli_stmt_close($stmt_table_state);

    
} catch (Exception $e) {
    echo "Error al encontrar ususario: " . $e->getMessage();
}

?>