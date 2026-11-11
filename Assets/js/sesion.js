// Control de la sesión en todas las pantallas del sistema
(function () {
    const urlSalida = base_url + '/login/logout?expirada=1';
    const fetchOriginal = window.fetch.bind(window);

    // Si el token ya no sirve (por ejemplo, entraste de nuevo en otra pestaña), se recarga la pantalla
    function tokenVencido() {
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

    // 1) Todo fetch que no sea GET lleva el token CSRF.
    //    Si el servidor responde 401 (la sesión terminó) se vuelve al login
    window.fetch = function (recurso, opciones) {
        opciones = opciones || {};
        const metodo = (opciones.method || 'GET').toUpperCase();
        if (metodo !== 'GET' && metodo !== 'HEAD') {
            const cabeceras = new Headers(opciones.headers || {});
            cabeceras.set('X-CSRF-Token', csrf_token);
            opciones = Object.assign({}, opciones, { headers: cabeceras });
        }
        return fetchOriginal(recurso, opciones).then(function (respuesta) {
            if (respuesta.status === 401) {
                window.location.href = urlSalida;
            } else if (respuesta.status === 403 && respuesta.headers.get('X-CSRF-Invalido')) {
                tokenVencido();
            }
            return respuesta;
        });
    };

    // Lo mismo para las peticiones de jQuery (por ejemplo, las tablas DataTables).
    // Se engancha otra vez cuando terminan de cargar los scripts, porque DataTables trae su propio jQuery
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
                window.location.href = urlSalida;
            } else if (xhr.status === 403 && xhr.getResponseHeader('X-CSRF-Invalido')) {
                tokenVencido();
            }
        });
    }
    engancharJquery();
    document.addEventListener('DOMContentLoaded', engancharJquery);

    // 2) Mientras alguien usa la pantalla, se avisa al servidor cada 5 minutos como mucho
    const cincoMinutos = 5 * 60 * 1000;
    let ultimoAviso = Date.now();
    function avisarAlServidor() {
        if (Date.now() - ultimoAviso < cincoMinutos) {
            return;
        }
        ultimoAviso = Date.now();
        fetchOriginal(base_url + '/login/ping')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.status) {
                    window.location.href = urlSalida;
                }
            })
            .catch(function () { });
    }

    // 3) Si nadie toca la pantalla durante el tiempo límite, se cierra la sesión
    let temporizador;
    function hayActividad() {
        avisarAlServidor();
        clearTimeout(temporizador);
        temporizador = setTimeout(function () {
            window.location.href = urlSalida;
        }, minutos_inactividad * 60 * 1000);
    }
    ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(function (evento) {
        document.addEventListener(evento, hayActividad, { passive: true });
    });
    hayActividad();
})();