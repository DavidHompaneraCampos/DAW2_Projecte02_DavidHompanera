<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "restaurante_bbdd";

// Intenta hacer la conexión
try {
    $conn = mysqli_connect($servername, $username, $password, $database);
    // Verifica la conexión
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error); // Lanza una excepción en caso de error
    }

} catch (Exception $e) {
    // Muestra por pantallal el error
    die("Error: " . $e->getMessage());
}
?>
