document.querySelectorAll('.abrirModal').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const idMesa = this.dataset.id;
        window.location.href = `?id=${idMesa}&id_sala=${new URLSearchParams(window.location.search).get('id_sala')}`;
    });
});

// Solo si necesitas cerrar el modal
if (document.getElementById('cerrar')) {
    document.getElementById('cerrar').addEventListener('click', function() {
        window.location.href = `?id_sala=${new URLSearchParams(window.location.search).get('id_sala')}`;
    });
}

// Validaciones de hora
const horaInput = document.getElementById('hora_reserva');
if (horaInput) {
    horaInput.addEventListener('change', function() {
        const hora = this.value;
        if (hora < "13:00" || hora > "23:00") {
            this.setCustomValidity('El horario de reservas es de 13:00 a 23:00');
        } else {
            this.setCustomValidity('');
        }
    });
}