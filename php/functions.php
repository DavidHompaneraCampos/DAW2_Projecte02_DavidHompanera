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
    $id_camarero = mysqli_real_escape_string($conn, $id_camarero);

    // Query para seleccionar la info del camarero
    $query = "SELECT * FROM tbl_camarero WHERE id_camarero  = ?";

    $stmt_info = mysqli_stmt_init($conn);

    // Preparamos la consulta
    if (mysqli_stmt_prepare($stmt_info, $query)) {
        mysqli_stmt_bind_param($stmt_info, 'i', $id_camarero);
        mysqli_stmt_execute($stmt_info);

        $result = mysqli_stmt_get_result($stmt_info);
        $row = mysqli_fetch_assoc($result);

        // Cerramos el stmt
        mysqli_stmt_close($stmt_info);

        return ['username' => $row['username'], 'name' => $row['nombre_camarero'], 'surname' => $row['apellidos_camarero']];
    }
}
?>