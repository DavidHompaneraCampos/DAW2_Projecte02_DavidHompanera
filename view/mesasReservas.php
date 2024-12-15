<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit();
}

require '../php/conexion.php';
require '../php/reservas/backReservas.php';
require_once '../php/functions.php';

$id_camarero = $_SESSION['user_id'];
actualizarReservasCompletadas($conn);
$info_waiter = get_info_waiter_bbdd($conn, $id_camarero);

$id_sala = $_GET['id_sala'] ?? null;

if (!$id_sala) {
    die("ID de sala no proporcionado.");
}

// Consulta para obtener solo las reservas confirmadas actuales y futuras
$queryReservas = "SELECT 
    r.id_reserva,
    r.id_mesa,
    r.fecha_reserva,
    r.estado_reserva
    FROM tbl_reservas r 
    WHERE r.fecha_reserva >= CURDATE()
    AND r.estado_reserva = 'Confirmada'
    ORDER BY r.fecha_reserva ASC";

$stmt = $conn->prepare($queryReservas);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizamos las reservas por mesa
$_SESSION['ARRAYreservas'] = array();
foreach ($reservas as $reserva) {
    $_SESSION['ARRAYreservas'][$reserva['id_mesa']][] = $reserva;
}

// Consultar las mesas de la sala seleccionada con la información de la sala
$queryMesas = "SELECT m.id_mesa, m.numero_sillas_mesa, s.ubicacion_sala as ubicacion 
               FROM tbl_mesa m 
               INNER JOIN tbl_sala s ON m.id_sala = s.id_sala 
               WHERE m.id_sala = :id_sala";
$stmt = $conn->prepare($queryMesas);
$stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
$stmt->execute();
$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
            <span class="span_subtitle">Gestión de Reservas</span>
        </div>

        <!-- Contenedor de navegación -->
        <nav id="nav_header">
            <a href="./reservas.php" class="btn btn-danger me-2 btn_custom_logOut">Volver</a>
            <a href="./mesas.php" class="btn btn-danger me-2 btn_custom_logOut">Ocupaciones</a>
            <a href="./historicoReservas.php" class="btn btn-danger me-2 btn_custom_logOut">Histórico</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <main class="container">
        <div class="row">
            <?php foreach ($mesas as $mesa): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Mesa <?php echo htmlspecialchars($mesa['id_mesa']); ?></h5>
                            <?php if (isset($_SESSION['ARRAYreservas'][$mesa['id_mesa']])): ?>
                                <?php foreach ($_SESSION['ARRAYreservas'][$mesa['id_mesa']] as $reserva): ?>
                                    <p class="reserva-item" style="cursor: pointer;" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#modalCancelar"
                                           data-id-reserva="<?php echo htmlspecialchars($reserva['id_reserva']); ?>"
                                           data-fecha="<?php echo htmlspecialchars($reserva['fecha_reserva']); ?>"
                                           data-estado="<?php echo htmlspecialchars($reserva['estado_reserva']); ?>"
                                           data-id-sala="<?php echo htmlspecialchars($id_sala); ?>">
                                            Reserva: <?php echo htmlspecialchars($reserva['fecha_reserva']); ?> 
                                            <br>Estado: <?php echo htmlspecialchars($reserva['estado_reserva']); ?>
                                        </p>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Mesa Disponible</p>
                            <?php endif; ?>

                            <a href="#" class="btn btn-primary abrirModal btn-danger btn_custom_filter" 
                               data-id="<?php echo htmlspecialchars($mesa['id_mesa']); ?>"
                               data-sillas="<?php echo htmlspecialchars($mesa['numero_sillas_mesa']); ?>"
                               data-ubicacion="<?php echo htmlspecialchars($mesa['ubicacion']); ?>">
                               Reservar Mesa
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal para nueva reserva -->
    <?php
    if (isset($_GET['id']) && ($_GET['id'] != "")) {
        $id = htmlspecialchars($_GET['id']);
    ?>
    <div id="contenedorMesas">
        <span class="close" id="cerrar">&times;</span>
        <div id="tituloMesas">
            <h3>Reservar Mesa</h3>
        </div>
        <?php
        $queryMesas = "SELECT 
                        tbl_mesa.id_mesa,
                        tbl_mesa.numero_sillas_mesa,
                        tbl_sala.ubicacion_sala AS sala
                    FROM 
                        tbl_mesa
                    INNER JOIN 
                        tbl_sala ON tbl_mesa.id_sala = tbl_sala.id_sala
                    WHERE 
                        tbl_mesa.id_mesa = :id";

        $stmt_table_estado = $conn->prepare($queryMesas);
        $stmt_table_estado->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_table_estado->execute();
        $result = $stmt_table_estado->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "Número de la mesa: " . htmlspecialchars($result["id_mesa"]) . "<br>";
            echo "Número de sillas: " . htmlspecialchars($result["numero_sillas_mesa"]) . "<br>";
            echo "<div style='margin-bottom: 1rem !important;'>Ubicación de la mesa: " . htmlspecialchars($result["sala"]) . "</div>";
            ?>
            <form action="../php/reservas/nuevaReserva.php" method="POST">
                <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($result['id_mesa']); ?>">
                <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($id_sala); ?>">
                <div class="mb-3">
                    <label for="fecha_reserva" class="form-label">Fecha de Reserva</label>
                    <input type="date"
                           class="form-control"
                           name="fecha_reserva"
                           id="fecha_reserva"
                           min="<?php echo date('Y-m-d'); ?>"
                           required>
                </div>
                <div class="mb-3">
                    <label for="hora_reserva" class="form-label">Hora de Reserva</label>
                    <input type="time"
                           class="form-control"
                           id="hora_reserva"
                           name="hora_reserva"
                           min="13:00"
                           max="23:00"
                           oninvalid="this.setCustomValidity('El horario de reservas es de 13:00 a 23:00')"
                           oninput="this.setCustomValidity('')"
                           required>
                    <small class="text-muted">Horario de reservas: 13:00 - 23:00</small>
                </div>
                <button type="submit" class="btn btn-danger btn_custom_filter">CONFIRMAR RESERVA</button>
            </form>
            <?php
        } else {
            echo "Esta mesa no existe";
        }
        ?>
    </div>
    <?php
    }
    ?>

    <!-- Modal para cancelar reserva -->
    <div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Fecha: <span id="modalFechaReserva"></span></p>
                    <p>Estado: <span id="modalEstadoReserva"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnCancelarReserva">Cancelar Reserva</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($_SESSION['error']); ?>',
            confirmButtonColor: '#dc3545'
        });
    </script>
    <?php 
    unset($_SESSION['error']);
    endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            confirmButtonColor: '#28a745'
        });
    </script>
    <?php 
    unset($_SESSION['success']);
    endif; ?>

    <script src="../js/modalCancelarReserva.js"></script>
    <script src="../js/modalReservas.js"></script>
</body>
</html>