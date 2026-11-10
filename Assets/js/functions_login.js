const formLogin = document.getElementById('formLogin');
const alerta = document.getElementById('alerta');

function mostrarAlerta(mensaje) {
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none');
}

async function enterLogin(e) {
    e.preventDefault();
    const user = document.getElementById('user');
    const password = document.getElementById('password');

    if (user.value.trim() === '' || password.value === '') {
        mostrarAlerta('Escribe tu usuario y tu contraseña.');
        return;
    }

    // Se desactiva el botón para que no se envíe dos veces
    const boton = formLogin.querySelector('button[type="submit"]');
    boton.disabled = true;

    try {
        const respuesta = await fetch(baseUrl + '/login/log', {
            method: 'POST',
            body: new FormData(formLogin)
        });
        const result = await respuesta.json();

        if (result.status) {
            window.location = baseUrl + '/inicio';
            return;
        }
        mostrarAlerta(result.msg);
        password.value = '';
        password.focus();
    } catch (error) {
        mostrarAlerta('No se pudo conectar con el servidor. Inténtalo de nuevo.');
    }
    boton.disabled = false;
}