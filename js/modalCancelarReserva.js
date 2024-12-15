document.addEventListener('DOMContentLoaded', function() {
    const modalCancelar = document.getElementById('modalCancelar');
    modalCancelar.addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        const idReserva = trigger.getAttribute('data-id-reserva');
        const fecha = trigger.getAttribute('data-fecha');
        const estado = trigger.getAttribute('data-estado');
        const idSala = trigger.getAttribute('data-id-sala');
        
        document.getElementById('modalFechaReserva').textContent = fecha;
        document.getElementById('modalEstadoReserva').textContent = estado;
        
        const btnCancelar = document.getElementById('btnCancelarReserva');
        btnCancelar.onclick = function() {
            window.location.href = `../php/reservas/actualizarReserva.php?id=${idReserva}&action=cancelar&id_sala=${idSala}`;
        };
    });
});