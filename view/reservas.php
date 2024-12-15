<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header("Location: ./index.php");
        exit();
    }

    require '../php/conexion.php';
    require '../php/estadoMesaRecuperar.php';
    require_once '../php/functions.php';

    $id_camarero = $_SESSION['user_id'];
    $info_waiter = get_info_waiter_bbdd($conn, $id_camarero);
    
    $querySalas = "SELECT id_sala, ubicacion_sala, imagen_sala FROM tbl_sala";
    $stmt = $conn->prepare($querySalas);
    $stmt->execute();
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    actualizarReservasCompletadas($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mesas.css">
</head>
<body>
    <!-- Cabecera -->
    <header id="container_header">
        <!-- Contenedor del usuario -->
        <div id="container-username">
            <!-- icono del usuario -->
            <div id="icon_profile_header">
                <img src="../img/logoSinFondo.png" alt="" id="icon_profile">
            </div>
            <!-- Contenedor de la información del usuario -->
            <div id="username_profile_header">
                <p id="p_username_profile"><?php echo htmlspecialchars($info_waiter['username'] ?? 'Desconocido'); ?></p>
                <span class="span_subtitle">
                    <?php echo htmlspecialchars(($info_waiter['name'] ?? 'Usuario') . " " . ($info_waiter['surname'] ?? 'Desconocido')); ?>
                </span>
            </div>
        </div>

        <!-- Contenedor del título de la página -->
        <div id="container_title_header">
            <h1 id="title_header"><strong>Dinner At Westfield</strong></h1>
            <span class="span_subtitle">Gestión de Ocupaicones</span>
        </div>

        <!-- Contenedor de navegación -->
        <nav id="nav_header">
            <a href="./mesas.php" class="btn btn-danger me-2 btn_custom_logOut">Ocupaciones</a>
            <a href="./historicoReservas.php" class="btn btn-danger me-2 btn_custom_logOut">Histórico</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <main class="container">
        <div class="row">
            <?php foreach ($salas as $sala): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <?php if (!empty($sala['imagen_sala'])): ?>
                            <img src="../img/<?php echo htmlspecialchars($sala['imagen_sala']); ?>" class="card-img-top" alt="Imagen de Sala">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($sala['ubicacion_sala']); ?></h5>
                            <p class="card-text">Ubicación: <?php echo htmlspecialchars($sala['ubicacion_sala']); ?></p>
                            <a href="mesasReservas.php?id_sala=<?php echo htmlspecialchars($sala['id_sala']); ?>" class="btn btn-primary btn-danger btn_custom_filter">Ver Mesas</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>