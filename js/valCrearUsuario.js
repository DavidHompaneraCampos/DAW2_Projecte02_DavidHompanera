// Función para verificar el formulario completo
function veriForm() {
    const errores = [
        document.getElementById("errorUsername").innerHTML,
        document.getElementById("errorNombre").innerHTML,
        document.getElementById("errorApellidos").innerHTML,
        document.getElementById("errorPassword").innerHTML,
        document.getElementById("errorPassword2").innerHTML,
        document.getElementById("errorRol").innerHTML
    ]
    const campos = [
        document.getElementById("username").value.trim(),
        document.getElementById("nombre").value.trim(),
        document.getElementById("apellidos").value.trim(),
        document.getElementById("password").value.trim(),
        document.getElementById("password2").value.trim(),
        document.getElementById("rol").value.trim()
    ]
    const camposVacios = campos.some(campo => campo == "")
    const hayErrores = errores.some(error => error !== "")
    document.getElementById('submitBtn').disabled = hayErrores || camposVacios
}

// Validación username
document.getElementById("username").oninput = function validaUsername() {
    let username = this.value.trim();
    let errorUsername = "";
    if(username.length == 0 || username == null || /^\s+$/.test(username)) {
        errorUsername = "El campo nombre de usuario no puede estar vacío";
    } else if(!letrasYnumeros(username)) {
        errorUsername = "El nombre de usuario solo puede contener letras y números";
    }
    function letrasYnumeros(username) {
        return /^[a-zA-Z0-9]+$/.test(username);
    }
    document.getElementById("errorUsername").innerHTML = errorUsername;
    veriForm();
}

// Validación nombre
document.getElementById("nombre").oninput = function validaNombre() {
    let nombre = this.value.trim();
    let errorNombre = "";
    if(nombre.length == 0 || nombre == null || /^\s+$/.test(nombre)) {
        errorNombre = "El campo nombre no puede estar vacío";
    } else if(!letras(nombre)) {
        errorNombre = "El nombre solo puede contener letras";
    }
    function letras(nombre) {
        return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre);
    }
    document.getElementById("errorNombre").innerHTML = errorNombre;
    veriForm();
}

// Validación apellidos
document.getElementById("apellidos").oninput = function validaApellidos() {
    let apellidos = this.value.trim();
    let errorApellidos = "";
    if(apellidos.length == 0 || apellidos == null || /^\s+$/.test(apellidos)) {
        errorApellidos = "El campo apellidos no puede estar vacío";
    } else if(!letras(apellidos)) {
        errorApellidos = "Los apellidos solo pueden contener letras";
    }
    function letras(apellidos) {
        return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellidos);
    }
    document.getElementById("errorApellidos").innerHTML = errorApellidos;
    veriForm();
}

// Validación contraseña
document.getElementById("password").oninput = function validaPassword() {
    let password = this.value;
    let errorPassword = "";
    
    if(password.length == 0 || password == null) {
        errorPassword = "El campo contraseña no puede estar vacío";
    } else if(password.length < 8) {
        errorPassword = "La contraseña debe tener al menos 8 caracteres";
    } else if(!validarFormatoPassword(password)) {
        errorPassword = "La contraseña debe contener al menos una mayúscula, una minúscula y un número";
    }

    function validarFormatoPassword(password) {
        // Al menos una mayúscula, una minúscula y un número
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password);
    }

    document.getElementById("errorPassword").innerHTML = errorPassword;
    validaPassword2(); // Validar también la confirmación
    veriForm();
}

// Validación confirmar contraseña
document.getElementById("password2").oninput = function validaPassword2() {
    let password2 = this.value;
    let password = document.getElementById("password").value;
    let errorPassword2 = "";
    if(password2.length == 0 || password2 == null) {
        errorPassword2 = "El campo confirmar contraseña no puede estar vacío";
    } else if(password2 !== password) {
        errorPassword2 = "Las contraseñas no coinciden";
    }
    document.getElementById("errorPassword2").innerHTML = errorPassword2;
    veriForm();
}

// Validación rol
document.getElementById("rol").onchange = function validaRol() {
    let rol = this.value;
    let errorRol = "";
    if(!rol) {
        errorRol = "Debe seleccionar un rol";
    }
    document.getElementById("errorRol").innerHTML = errorRol;
    veriForm();
}