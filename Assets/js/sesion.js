// Control de la sesión en todas las pantallas del sistema
(function () {
    const urlSalida = base_url + '/login/logout?expirada=1';
    const fetchOriginal = window.fetch.bind(window);
    let saliendo = false;

    // Vuelve al login cuando la sesión ya no sirve
    function irAlLogin(url) {
        if (!saliendo) {
            saliendo = true;
            window.location.href = url;
        }
    }

    // Si el token ya no sirve (por ejemplo, se volvió a iniciar sesión en otra pestaña), se recarga la pantalla
    function tokenVencido() {
        if (saliendo) {
            return;
        }
        saliendo = true;
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'La página se desactualizó',
                text: 'Se va a recargar. Vuelve a hacer lo que estabas haciendo.'
            }).then(function () { window.location.reload(); });
        } else {
            window.location.reload();
        }
    }

    function esDeEsteSistema(recurso) {
        try {
            return new URL(String(recurso), window.location.href).origin === window.location.origin;
        } catch (e) {
            return false;
        }
    }

    // 1) Peticiones hechas con fetch
    window.fetch = function (recurso, opciones) {
        // Solo se tocan las peticiones a este mismo sistema: el token nunca sale hacia otros sitios
        if (recurso instanceof Request || !esDeEsteSistema(recurso)) {
            return fetchOriginal(recurso, opciones);
        }

        opciones = Object.assign({}, opciones);
        const cabeceras = new Headers(opciones.headers || {});
        // Le avisa al servidor que la petición viene de JavaScript, para que responda en JSON
        cabeceras.set('X-Requested-With', 'XMLHttpRequest');
        const metodo = (opciones.method || 'GET').toUpperCase();
        if (metodo !== 'GET' && metodo !== 'HEAD') {
            cabeceras.set('X-CSRF-Token', csrf_token);
        }
        opciones.headers = cabeceras;

        return fetchOriginal(recurso, opciones).then(function (respuesta) {
            const sesionTerminada = respuesta.status === 401;
            const tokenInvalido = respuesta.status === 403 && respuesta.headers.get('X-CSRF-Invalido');
            if (!sesionTerminada && !tokenInvalido) {
                return respuesta;
            }
            if (sesionTerminada) {
                irAlLogin(urlSalida);
            } else {
                tokenVencido();
            }
            // La pantalla va a cambiar o recargarse: la respuesta no sigue su camino,
            // así no aparece además un mensaje de error de la propia pantalla
            return new Promise(function () { });
        });
    };

    // 2) Peticiones hechas con jQuery (por ejemplo, las tablas DataTables).
    //    Se engancha otra vez cuando terminan de cargar los scripts, porque DataTables trae su propio jQuery
    function engancharJquery() {
        const $j = window.jQuery;
        if (!$j || $j.sesionEnganchada) {
            return;
        }
        $j.sesionEnganchada = true;
        // El token solo acompaña a las peticiones que cambian datos en este mismo sistema.
        // Nunca viaja a otros sitios (por ejemplo, al servidor de donde DataTables baja el idioma)
        $j.ajaxPrefilter(function (opciones, originales, xhr) {
            const metodo = (opciones.type || 'GET').toUpperCase();
            if (!opciones.crossDomain && metodo !== 'GET' && metodo !== 'HEAD') {
                xhr.setRequestHeader('X-CSRF-Token', csrf_token);
            }
        });
        $j(document).ajaxError(function (evento, xhr) {
            if (xhr.status === 401) {
                irAlLogin(urlSalida);
            } else if (xhr.status === 403 && xhr.getResponseHeader('X-CSRF-Invalido')) {
                tokenVencido();
            }
        });
        // Si la sesión terminó, DataTables no muestra su propio aviso de error: ya se está volviendo al login
        $j(document).on('xhr.dt', function (evento, ajustes, json, xhr) {
            if (xhr && xhr.status === 401) {
                return true;
            }
        });
    }
    engancharJquery();
    document.addEventListener('DOMContentLoaded', engancharJquery);

    // 3) Cierre por inactividad
    const limite = minutos_inactividad * 60 * 1000;
    const paso = Math.min(15000, limite / 4);          // cada cuánto se revisa si hubo uso
    const pausaAvisos = Math.min(60000, limite / 4);   // cada cuánto, como mucho, se avisa al servidor
    const CLAVE_USO = 'chickenlindo_ultimo_uso';
    let ultimoUso = Date.now();
    let ultimoGuardado = 0;
    let ultimoAviso = Date.now();

    // Pregunta al servidor si la sesión sigue abierta; si no, vuelve al login
    function comprobarSesion(urlSiTermino) {
        fetchOriginal(base_url + '/login/ping')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.status) {
                    irAlLogin(urlSiTermino);
                }
            })
            .catch(function () { });
    }

    function hayActividad() {
        ultimoUso = Date.now();
        // La hora del último uso se comparte entre las pestañas abiertas,
        // para que una pestaña olvidada no cierre la sesión de la que se está usando
        if (ultimoUso - ultimoGuardado >= paso) {
            ultimoGuardado = ultimoUso;
            try { localStorage.setItem(CLAVE_USO, String(ultimoUso)); } catch (e) { }
        }
        // Mientras alguien usa el sistema, el servidor se entera y no cierra la sesión
        if (ultimoUso - ultimoAviso >= pausaAvisos) {
            ultimoAviso = ultimoUso;
            comprobarSesion(urlSalida);
        }
    }

    // Si nadie usó el sistema en ninguna pestaña durante el tiempo límite, se cierra la sesión
    function revisarInactividad() {
        let usoEnOtraPestana = 0;
        try { usoEnOtraPestana = parseInt(localStorage.getItem(CLAVE_USO), 10) || 0; } catch (e) { }
        if (Date.now() - Math.max(ultimoUso, usoEnOtraPestana) > limite) {
            irAlLogin(urlSalida);
        }
    }

    ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(function (evento) {
        document.addEventListener(evento, hayActividad, { passive: true });
    });
    setInterval(revisarInactividad, paso);
    document.addEventListener('visibilitychange', revisarInactividad);
    hayActividad();

    // 4) Si en otra pestaña se cerró la sesión (llegó a la pantalla de login), esta también sale
    window.addEventListener('storage', function (evento) {
        if (evento.key === 'chickenlindo_sin_sesion' && evento.newValue) {
            const expirada = evento.newValue.indexOf('expirada') === 0;
            comprobarSesion(base_url + '/login' + (expirada ? '?expirada=1' : ''));
        }
    });
})();