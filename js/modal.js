const botonMesas = document.querySelectorAll('.mesa');

botonMesas.forEach(function (botonMesas) {
    if (botonMesas) {
        botonMesas.addEventListener('click', function () {    
            window.location.href = `../view/mesas.php?id=${botonMesas.id}`;
        });
    }
});

// Obtener el elemento de la "X" (el botón de cierre)
var cerrarModal = document.getElementById("cerrar");

function cerrarVentanaModal() {
    var contenedor = document.getElementById("contenedorMesas");
    contenedor.style.display = "none"; // Oculta el contenedor que contiene el modal
}

cerrarModal.onclick = function() {
    cerrarVentanaModal();
}