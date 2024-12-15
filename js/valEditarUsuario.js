// Función para verificar que el formulario está listo para enviar
function verificarFormulario() {
    const errores = [
        document.getElementById("errorUsername").innerHTML,
        document.getElementById("errorNombre").innerHTML,
        document.getElementById("errorApellidos").innerHTML,
        document.getElementById("errorPassword").innerHTML,
        document.getElementById("errorPassword2").innerHTML,
        document.getElementById("errorRol").innerHTML
    ];
    const campos = [
        document.getElementById("username").value.trim(),
        document.getElementById("nombre").value.trim(),
        document.getElementById("apellidos").value.trim(),
        document.getElementById("rol").value.trim()
    ];
    const hayErrores = errores.some(error => error !== "");
    const camposVacios = campos.some(campo => campo === "");

    document.getElementById("submitBtn").disabled = hayErrores || camposVacios;
}

// Validación del nombre de usuario
document.getElementById("username").oninput = function () {
    let username = this.value.trim();
    let error = "";

    if (username === "") {
        error = "El nombre de usuario no puede estar vacío";
    } else if (!/^[a-zA-Z0-9]+$/.test(username)) {
        error = "El nombre de usuario solo puede contener letras y números";
    }

    document.getElementById("errorUsername").innerHTML = error;
    verificarFormulario();
};

// Validación del nombre
document.getElementById("nombre").oninput = function () {
    let nombre = this.value.trim();
    let error = "";

    if (nombre === "") {
        error = "El nombre no puede estar vacío";
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/.test(nombre)) {
        error = "El nombre solo puede contener letras y espacios";
    }

    document.getElementById("errorNombre").innerHTML = error;
    verificarFormulario();
};

// Validación de apellidos
document.getElementById("apellidos").oninput = function () {
    let apellidos = this.value.trim();
    let error = "";

    if (apellidos === "") {
        error = "Los apellidos no pueden estar vacíos";
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/.test(apellidos)) {
        error = "Los apellidos solo pueden contener letras y espacios";
    }

    document.getElementById("errorApellidos").innerHTML = error;
    verificarFormulario();
};

// Validación de contraseña
document.getElementById("password").oninput = function () {
    let password = this.value.trim();
    let error = "";

    if (password !== "" && password.length < 8) {
        error = "La contraseña debe tener al menos 8 caracteres";
    }

    document.getElementById("errorPassword").innerHTML = error;
    validarRepetirPassword(); // Validar la confirmación también
    verificarFormulario();
};

// Validación de repetir contraseña
document.getElementById("password2").oninput = validarRepetirPassword;

function validarRepetirPassword() {
    let password = document.getElementById("password").value.trim();
    let password2 = document.getElementById("password2").value.trim();
    let error = "";

    if (password2 !== "" && password2 !== password) {
        error = "Las contraseñas no coinciden";
    }

    document.getElementById("errorPassword2").innerHTML = error;
    verificarFormulario();
}

// Validación de selección de rol
document.getElementById("rol").onchange = function () {
    let rol = this.value.trim();
    let error = "";

    if (rol === "") {
        error = "Debe seleccionar un rol válido";
    }

    document.getElementById("errorRol").innerHTML = error;
    verificarFormulario();
};