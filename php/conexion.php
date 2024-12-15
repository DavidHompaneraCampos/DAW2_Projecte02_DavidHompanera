<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "restaurante2_bbdd";

try {
    // Crea la conexión con PDO
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);

    // Configura el modo de error de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mensaje opcional (puedes eliminarlo en producción)
    // echo "Conexión exitosa";
} catch (PDOException $e) {
    // Maneja errores de conexión
    die("Error de conexión: " . $e->getMessage());
}
?>