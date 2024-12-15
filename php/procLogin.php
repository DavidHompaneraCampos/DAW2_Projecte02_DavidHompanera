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
$username = htmlspecialchars($_POST['user']);
$password = htmlspecialchars($_POST['contrasena']);

try {
    // Modificamos la consulta para unir con la tabla de roles
    $query = "SELECT u.id_usuario, u.password, r.nombre_rol 
              FROM tbl_usuario u 
              INNER JOIN tbl_roles r ON u.id_rol = r.id_rol 
              WHERE u.username = :username";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id_usuario'];
            $_SESSION['rol'] = $row['nombre_rol'];

            if ($row['nombre_rol'] === 'Administrador') {
                echo "<script type='text/javascript'>
                    Swal.fire({
                        title: 'Inicio de sesión',
                        text: '¡Bienvenido Administrador!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = '../view/gestionUsuarios.php';
                    });
                    </script>";
            } else {
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
            }
            exit();
        }
    }

    $errors[] = 'Credenciales incorrectas';
} catch (PDOException $e) {
    $errors[] = "Error en la base de datos: " . $e->getMessage();
}

// Redirección en caso de error
redirect_with_errors('../view/index.php', $errors);
?>
</body>
</html>