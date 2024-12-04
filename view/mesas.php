<?php

// Iniciamos la sesión
session_start();

// Verificamos si la sesión del camarero está activa
if (empty($_SESSION['user_id'])) {
    // Si no está activo, redirigimos a la página de inicio de sesión
    header("Location: ./index.php");
    exit();
}

// Incluimos el archivo de conexión a la base de datos
require '../php/conexion.php';
require '../php/estadoMesaRecuperar.php';
require_once '../php/functions.php';

// Obtenemos el ID del camarero desde la sesión
$id_camarero = $_SESSION['user_id'];

// Recojemos la información del usuario que está guardado en la BBDD
$info_waiter = get_info_waiter_bbdd($conn, $id_camarero);

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
                <!-- icono del usuario  -->
                <div id="icon_profile_header">
                    <img src="../img/logoSinFondo.png" alt="" id="icon_profile">
                </div>
                <!-- Contenedor de la información del usuario -->
                <div id="username_profile_header">
                    <p id="p_username_profile"><?php echo htmlspecialchars($info_waiter['username']) ?></p>
                    <span class="span_subtitle"><?php echo htmlspecialchars($info_waiter['name']) . " " . htmlspecialchars($info_waiter['surname']) ?></span>
                </div>
            </div>

            <!-- Contenedor del título de la página -->
            <div id="container_title_header">
                <h1 id="title_header"><strong>Dinner At Westfield</strong></h1>
                <span class="span_subtitle">Gestión de mesas</span>
            </div>

            <!-- Contenedor de navegación -->
            <nav id="nav_header">
                <a href="./historico.php" class="btn btn-danger me-2 btn_custom_logOut">Histórico reservas</a>
                <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
            </nav>
        </header>

    <main id="mesas_main">
        <div id="mapaRestaurante_contenedor">
            <img src="../img/mapa_restaurante2.png" alt="" id="mapa">
            <div id="divGrande">
                <div id="divEntrada">
                </div>
                <div id="divSalas">
                    <div id="divLados">
                        <div id="divTerrazasLados" class="terrazasVerticales">
                            <img src="<?php 
                                if ($ARRAYocupaciones["1"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="1">

                            <img src="<?php 
                                if ($ARRAYocupaciones["7"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="7" >
                        </div>

                        <div id="divSalaPriv" class="comunSalasMesa">
                            <img src="<?php 
                                if ($ARRAYocupaciones["13"] === "Ocupado") {
                                    echo '../img/mesaD4ocupada.png';
                                } else {
                                    echo '../img/mesaD4.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="13">

                        </div>
                    </div>
                    <div id="divSala1">
                        <div class="salaGrandeDividida">
                        <img src="<?php 
                                if ($ARRAYocupaciones["2"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="2">
                            
                            <img src="<?php 
                                if ($ARRAYocupaciones["8"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="8">

                            <img src="<?php 
                                if ($ARRAYocupaciones["14"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="14">
                        </div>

                        <div class="salaGrandeDividida">

                            <img src="<?php 
                                if ($ARRAYocupaciones["3"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="3">

                            <img src="<?php 
                                if ($ARRAYocupaciones["9"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="9">

                            <img src="<?php 
                                if ($ARRAYocupaciones["15"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="15">

                        </div>
                    </div>

                    <div id="divSala2">
                        <div class="salaGrandeDividida">
                            <img src="<?php 
                                if ($ARRAYocupaciones["4"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="4">
                            
                            <img src="<?php 
                                if ($ARRAYocupaciones["10"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="10">

                            <img src="<?php 
                                if ($ARRAYocupaciones["16"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="16">
                        </div>
                        <div class="salaGrandeDividida">
                            <img src="<?php 
                                if ($ARRAYocupaciones["5"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="5">

                            <img src="<?php 
                                if ($ARRAYocupaciones["11"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="11">

                            <img src="<?php 
                                if ($ARRAYocupaciones["17"] === "Ocupado") {
                                    echo '../img/mesaD8ocupada.png';
                                } else {
                                    echo '../img/mesaD8.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="17">
                        </div>
                    </div>
                    <div id="divLados">
                        <div id="divTerrazasLados" class="terrazasVerticales">
                            <img src="<?php 
                                if ($ARRAYocupaciones["6"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="6">
                            
                            <img src="<?php 
                                if ($ARRAYocupaciones["12"] === "Ocupado") {
                                    echo '../img/mesaD6ocupada.png';
                                } else {
                                    echo '../img/mesaD6.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="12">
                            

                        </div>
                        <div id="divSalaPriv" class="comunSalasMesa">
                        <img src="<?php 
                                if ($ARRAYocupaciones["18"] === "Ocupado") {
                                    echo '../img/mesaD4ocupada.png';
                                } else {
                                    echo '../img/mesaD4.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="18">

                        </div>
                    </div>
                </div>
                <div id="divAbajo">
                    <div id="divSalaPrivAbajo" class="comunSalasMesa">
                        <img src="<?php 
                                if ($ARRAYocupaciones["19"] === "Ocupado") {
                                    echo '../img/mesaD4ocupada.png';
                                } else {
                                    echo '../img/mesaD4.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="19">

                    </div>
                    <div id="divTerraza3" class="comunSalasMesas">

                        <img src="<?php 
                                if ($ARRAYocupaciones["20"] === "Ocupado") {
                                    echo '../img/mesaD2ocupada.png';
                                } else {
                                    echo '../img/mesaD2.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="20">

                        <img src="<?php 
                                if ($ARRAYocupaciones["21"] === "Ocupado") {
                                    echo '../img/mesaD4ocupada.png';
                                } else {
                                    echo '../img/mesaD4.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="21">

                        <img src="<?php 
                                if ($ARRAYocupaciones["22"] === "Ocupado") {
                                    echo '../img/mesaD4ocupada.png';
                                } else {
                                    echo '../img/mesaD4.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="22">

                        <img src="<?php 
                                if ($ARRAYocupaciones["23"] === "Ocupado") {
                                    echo '../img/mesaD2ocupada.png';
                                } else {
                                    echo '../img/mesaD2.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="23">

                    </div>
                    <div id="divSalaPrivAbajo" class="comunSalasMesa">
                        
                        <img src="<?php 
                                if ($ARRAYocupaciones["24"] === "Ocupado") {
                                    echo '../img/mesaD4ocupada.png';
                                } else {
                                    echo '../img/mesaD4.png';
                                }
                            ?>" alt="" class="mesa"  style="display: block;" id="24">
                            
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php
   if (isset($_GET['id']) && ($_GET['id'] != "")) {
        $id = htmlspecialchars($_GET['id']);
    ?>
    <div id="contenedorMesas">
        <span class="close" id="cerrar">&times;</span>
            <div id="tituloMesas">
                <h3>Mesa</h3>
            </div>
            <div class="form-group row">
            </div>
            <?php
            $queryMesas = "SELECT 
                                tbl_mesa.id_mesa,
                                tbl_mesa.numero_sillas_mesa,
                                tbl_sala.ubicacion_sala AS sala,
                                tbl_ocupacion.id_ocupacion,
                                tbl_ocupacion.estado_ocupacion AS estado
                            FROM 
                                tbl_mesa
                            INNER JOIN 
                                tbl_sala ON tbl_mesa.id_sala = tbl_sala.id_sala
                            INNER JOIN 
                                tbl_ocupacion ON tbl_mesa.id_mesa = tbl_ocupacion.id_mesa
                            WHERE 
                                tbl_mesa.id_mesa = ?;";

            $stmt_table_estado = mysqli_prepare($conn, $queryMesas);
            mysqli_stmt_bind_param($stmt_table_estado, "i", $id);

            // Ejecutar la declaración
            mysqli_stmt_execute($stmt_table_estado);

            // Obtener el resultado
            $result = mysqli_stmt_get_result($stmt_table_estado);
            
            if (mysqli_num_rows($result) > 0) {
                // Obtener los datos de la mesa
                $mesaInfo = mysqli_fetch_assoc($result);

                echo "Id de la mesa: " . $mesaInfo["id_mesa"] . "<br>";
                echo "Numero de sillas: " . $mesaInfo["numero_sillas_mesa"] . "<br>";
                echo "Ubicacion de la mesa: " . $mesaInfo["sala"] . "<br>";

                if ($_SESSION['ARRAYocupaciones'][$mesaInfo["id_mesa"]] === "Ocupado") {
                    $queryCamarero = "SELECT 
                                        tbl_camarero.nombre_camarero AS nombre,
                                        tbl_camarero.apellidos_camarero AS apellidos
                                    FROM 
                                        tbl_mesa
                                    INNER JOIN 
                                        tbl_ocupacion ON tbl_mesa.id_mesa = tbl_ocupacion.id_mesa
                                    INNER JOIN 
                                        tbl_camarero ON tbl_camarero.id_camarero = tbl_ocupacion.id_camarero
                                    WHERE 
                                        tbl_mesa.id_mesa = ?
                                    AND 
                                        tbl_ocupacion.estado_ocupacion LIKE 'Ocupado';";

                    $stmt_camarero = mysqli_prepare($conn, $queryCamarero);
                    mysqli_stmt_bind_param($stmt_camarero, "i", $id);

                    // Ejecutar la declaración
                    mysqli_stmt_execute($stmt_camarero);

                    // Obtener el resultado
                    $result = mysqli_stmt_get_result($stmt_camarero);
                    $infoCamarero = mysqli_fetch_assoc($result);

                    echo "Camarero: " . $infoCamarero["nombre"] . " " . $infoCamarero['apellidos'] ."<br>";
                    echo'<br>';
                    echo'<br>';
                    echo '<a href="../php/liberarMesas.php?id=' . $mesaInfo['id_mesa'] . '"><button class="btn btn-danger btn_custom_filter">LIBERAR</button></a>';
                } else {
                    echo'<br>';
                    echo'<br>';
                    echo '<a href="../php/reservaMesas.php?id=' . $mesaInfo['id_mesa'] . '"><button class="btn btn-danger btn_custom_filter">OCUPAR</button></a>';

                }
            } else {
                echo "Esta mesa no existe";
            }

            ?>
    </div>

<?php
   }
   ?>

<script src="../js/modal.js"></script>
</body>
</html>