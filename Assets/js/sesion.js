// Control de la sesión en todas las pantallas del sistema
(function () {
    const urlSalida = base_url + '/login/logout?expirada=1';
    const fetchOriginal = window.fetch.bind(window);

    // 1) Si el servidor responde 401 (la sesión ya terminó), se vuelve al login
    window.fetch = function () {
        return fetchOriginal.apply(null, arguments).then(function (respuesta) {
            if (respuesta.status === 401) {
                window.location.href = urlSalida;
            }
            return respuesta;
        });
    };
    if (window.jQuery) {
        jQuery(document).ajaxError(function (evento, xhr) {
            if (xhr.status === 401) {
                window.location.href = urlSalida;
            }
        });
    }

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