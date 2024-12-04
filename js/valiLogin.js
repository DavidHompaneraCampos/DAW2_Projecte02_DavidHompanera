function validateUser() {
    const userField = document.getElementById("user");
    const userError = document.getElementById("userError");

    if (userField.value.trim() === "") {
        userError.textContent = "El usuario no puede estar vacío";
        userField.classList.add("error");
        return false;
    } 

    userError.textContent = "";
    userField.classList.remove("error");

    return true;  // Cambiado a true para indicar que la validación es correcta
}

function validatePassword() {
    const passwordField = document.getElementById("contrasena");
    const passwordError = document.getElementById("passwordError");

    if (passwordField.value.trim() === "") {
        passwordError.textContent = "La contraseña no puede estar vacía";
        passwordField.classList.add("error");
        return false;
    }

    passwordError.textContent = "";
    passwordField.classList.remove("error");

    return true;  // Cambiado a true para indicar que la validación es correcta
}

function validateLogin(event) {
    event.preventDefault();  // Evita que el formulario se envíe de inmediato

    // Llama a las funciones de validación
    const isUserValid = validateUser();
    const isPasswordValid = validatePassword();

    // Verifica si ambas validaciones son correctas
    if (isUserValid && isPasswordValid) {
        // Si las validaciones son correctas, envía el formulario
        document.forms["login_form"].submit();
    }
}
