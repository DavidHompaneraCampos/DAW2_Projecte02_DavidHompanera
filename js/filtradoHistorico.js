const button_filter = document.getElementById('filter_button');

if (button_filter) {
    button_filter.addEventListener('click', function () {

        let elementoPadre = document.getElementById("contenedorFiltros");

        elementoPadre.style.display = "flex";
    });
}

const cerrar_filtros = document.getElementById('cerrarFiltros');

if (cerrar_filtros) {
    cerrar_filtros.addEventListener('click', function () {
        let elementoPadre = document.getElementById("contenedorFiltros");
        elementoPadre.style.display = "none";
    });
}

// Agregar la validación en el evento submit del formulario
const formulario = document.getElementById('formFiltros');

if (formulario) {
    formulario.addEventListener('submit', function(event) {
        const selectElement = document.getElementById("opcionesSelect");

        if (selectElement && selectElement.value === "") {
            // Evitar el envío del formulario si no se ha seleccionado una opción válida
            event.preventDefault();
            alert("Por favor, selecciona una opción válida.");
        }
    });
}
