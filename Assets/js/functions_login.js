const formLogin = document.getElementById('formLogin');
const alerta = document.getElementById('alerta');

function mostrarAlerta(mensaje) {
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none');
}

function enviarLogin() {
    return fetch(baseUrl + '/login/log', {
        method: 'POST',
        // Le avisa al servidor que la petición viene de JavaScript, para que responda en JSON
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(formLogin)
    });
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
        let respuesta = await enviarLogin();

        // Si esta pantalla llevaba mucho tiempo abierta, su token ya venció:
        // se pide uno nuevo y se envía otra vez, sin molestar a quien está entrando
        if (respuesta.status === 403) {
            const nuevo = await (await fetch(baseUrl + '/login/token')).json();
            formLogin.querySelector('input[name="csrf_token"]').value = nuevo.token;
            respuesta = await enviarLogin();
        }

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