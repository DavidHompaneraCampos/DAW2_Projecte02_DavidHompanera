<?php
session_start();
require_once '../php/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: ../view/index.php');
    exit();
}

$query = "
    SELECT 
        s.id_sala, 
        s.ubicacion_sala, 
        COUNT(DISTINCT m.id_mesa) AS total_mesas,
        COUNT(si.id_silla) AS total_sillas
    FROM tbl_sala s
    LEFT JOIN tbl_mesa m ON s.id_sala = m.id_sala
    LEFT JOIN tbl_sillas si ON m.id_mesa = si.id_mesa
    GROUP BY s.id_sala, s.ubicacion_sala
    ORDER BY s.id_sala";

$stmt = $conn->prepare($query);
$stmt->execute();
$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/historicoResponsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Cabecera -->
    <header id="container_header">
        <div id="container-username">
            <div id="icon_profile_header">
                <img src="../img/logoSinFondo.png" alt="" id="icon_profile">
            </div>
            <div id="username_profile_header">
                <p id="p_username_profile">admin</p>
                <span class="span_subtitle">Admin Principal</span>
            </div>
        </div>
        <div id="container_title_header">
            <h1 id="title_header"><strong>Dinner At Westfield</strong></h1>
            <span class="span_subtitle">Gestión de Recursos</span>
        </div>
        <nav id="nav_header">
            <a href="./reservas.php" class="btn btn-danger me-2 btn_custom_logOut">Reservas</a>
            <a href="./mesas.php" class="btn btn-danger me-2 btn_custom_logOut">Ocupaciones</a>
            <a href="../php/cerrarSesion.php" class="btn btn-danger btn_custom_logOut m-1">Cerrar sesión</a>
        </nav>
    </header>

    <main class="container mt-5">
        <div id="headerTituloFiltros" class="d-flex justify-content-between align-items-center">
            <h2>Gestión de Recursos</h2>
            <div>
                <a href="gestionUsuarios.php" class="btn btn-danger btn_custom_filter me-2">Gestion de Usuarios</a>
                <a href="../php/crearRecurso.php" class="btn btn-danger btn_custom_filter me-2">
                    <i class="fas fa-plus"></i> Nuevo Recurso
                </a>
            </div>
        </div>

        <table class="table table-striped table-bordered mt-4">
        <thead class="table-active">
            <tr>
                <th>Ubicación</th>
                <th>Total Mesas</th>
                <th>Total Sillas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salas as $sala): ?>
                <tr onclick="window.location.href='gestionMesas.php?id_sala=<?php echo $sala['id_sala']; ?>'" style="cursor: pointer;" onmouseover="this.style.backgroundColor='rgba(0, 0, 0, 0.2)';" onmouseout="this.style.backgroundColor='rgba(0, 0, 0, 0.1)';">
                    <td><?php echo htmlspecialchars($sala['ubicacion_sala']); ?></td>
                    <td><?php echo htmlspecialchars($sala['total_mesas']); ?></td>
                    <td><?php echo htmlspecialchars($sala['total_sillas']); ?></td>
                    <td>
                        <a href="../php/editarRecurso.php?id_sala=<?php echo $sala['id_sala']; ?>" 
                        class="btn btn-warning btn-sm" onclick="event.stopPropagation();">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="event.stopPropagation(); confirmarEliminar(<?php echo $sala['id_sala']; ?>)" 
                                class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Bootstrap JS y SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `../php/eliminarRecurso.php?id_sala=${id}`;
            }
        })
    }
    </script>
</body>
</html>