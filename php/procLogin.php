<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>process</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
// Importamos los archivos necesarios
require '../php/conexion.php'; 
require_once '../php/functions.php'; 

$errors = [];

// Validamos que el formulario se envíe correctamente
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errors[] = 'Solicitud inválida.';
    redirect_with_errors('../php/cerrarSesion.php', $errors);
    exit();
}

// Validamos que los campos no estén vacíos
if (empty($_POST['user']) || empty($_POST['contrasena'])) {
    $errors[] = 'Usuario y contraseña son obligatorios.';
    redirect_with_errors('../php/cerrarSesion.php', $errors);
    exit();
}

// Recogemos las variables del formulario
$username = mysqli_real_escape_string($conn, htmlspecialchars($_POST['user']));
$password = htmlspecialchars($_POST['contrasena']);

// Preparamos la consulta
$query = "SELECT id_camarero, password FROM tbl_camarero WHERE username = ?";
$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt); 
    $result = mysqli_stmt_get_result($stmt);

    // Comprueba si hay resultado
    if ($row = mysqli_fetch_assoc($result)) {

        // Verificamos que la contraseña sea correcta
        if (password_verify($password, $row['password'])) {
            // En caso que sea correcto, inicializamos la variable de SESSION y redirijimos a mesas.php con el ID del usuario
            session_start();
            $_SESSION['user_id'] = $row['id_camarero'];

            // Cerramos las consultas y la conexión
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            // Redirección a mesas.php con SweetAlert
            echo "<script type='text/javascript'>
                Swal.fire({
                    title: 'Inicio de sesión',
                    text: '¡Has iniciado sesión correctamente!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = '../view/mesas.php';
                });
                </script>";
            exit();
        }
    }
    
    $errors[] = 'Credenciales incorrectas';

    // Cerramos las consultas
    mysqli_stmt_close($stmt);
}

// Cerramos la conexión
mysqli_close($conn);
redirect_with_errors('../view/index.php', $errors);
?>
</body>
</html>