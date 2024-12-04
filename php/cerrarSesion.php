<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>process</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
    session_start(); 
    if (isset($_SESSION['user_id'])) {
    session_unset(); 
    session_destroy(); 

    echo "<script type='text/javascript'>
                Swal.fire({
                    title: 'Cerrar sesión',
                    text:'¡Has cerrado sesión correctamente!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = '../view/index.php';
                });
            </script>";
    exit();
    } else {
        header('Location: ../view/index.php');
    }
?>
</body>
</html>