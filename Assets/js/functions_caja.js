var tableCaja;

document.addEventListener('DOMContentLoaded', function () {
    cargarEstadoCaja();

    tableCaja = $('#tableCaja').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" },
        "ajax": {
    "url": base_url + "/Caja/getHistorialCaja",
    "dataSrc": "",
    "error": function (xhr) {
        console.log('Error cargando historial de caja:', xhr.responseText);
        document.querySelector('#tableCaja tbody').innerHTML =
            '<tr><td colspan="7" class="text-center">No se pudo cargar el historial. Intenta recargar la página.</td></tr>';
    }
},
        "columns": [
            { data: "id_caja" },
            { data: "responsable" },
            { data: "fecha_apertura" },
            { data: "fecha_cierre" },
            { data: "efectivo_esperado" },
            { data: "efectivo_declarado" },
            { data: "diferencia" },
            { data: "estado_texto" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[0, "desc"]]
    });
});

const params = new URLSearchParams(window.location.search);
if (params.get('aviso') === 'caja_cerrada') {
    Swal.fire({
        icon: 'info',
        title: 'Primero abre la caja',
        text: 'Para registrar ventas necesitas aperturar caja.'
    });
    // Limpia el parámetro para que no vuelva a salir la alerta si refrescas la página
    window.history.replaceState({}, document.title, window.location.pathname);
}


function cargarEstadoCaja() {
    fetch(base_url + '/Caja/getEstadoCaja')
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.abierta) {
                document.getElementById('panelCajaCerrada').style.display = 'none';
                document.getElementById('panelCajaAbierta').style.display = 'block';
                document.getElementById('cajaFechaApertura').innerText = data.fecha_apertura;
                document.getElementById('cajaCantidadTickets').innerText = data.cantidad_tickets_actual;
                document.getElementById('cajaEfectivoEsperado').innerText = parseFloat(data.efectivo_esperado_actual).toFixed(2);
            } else {
                document.getElementById('panelCajaAbierta').style.display = 'none';
                document.getElementById('panelCajaCerrada').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('No se pudo cargar el estado de caja:', error);
            // Ante cualquier falla, mostramos el panel de "aperturar caja" en vez de dejar la pantalla en blanco
            document.getElementById('panelCajaAbierta').style.display = 'none';
            document.getElementById('panelCajaCerrada').style.display = 'block';
        });
}

function abrirCaja() {
    fetch(base_url + '/Caja/abrirCaja', { method: 'POST', body: new FormData() })
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                Swal.fire('¡Listo!', result.msg, 'success').then(() => location.reload());
            } else {
                Swal.fire('Aviso', result.msg, 'warning');
            }
        });
}

let modalCierreCaja = null;

function abrirModalCierreCaja() {
    document.getElementById('montoEfectivoEsperadoModal').innerText = document.getElementById('cajaEfectivoEsperado').innerText;
    document.getElementById('formCierreCaja').reset();
    modalCierreCaja = new bootstrap.Modal(document.getElementById('modalCierreCaja'));
    modalCierreCaja.show();
}

function cerrarCaja(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formCierreCaja'));

    fetch(base_url + '/Caja/cerrarCaja', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(result => {
            if (modalCierreCaja) modalCierreCaja.hide();

            // Bootstrap 4 tarda ~300ms en la animación de cierre del modal;
            // esperamos a que termine antes de mostrar el SweetAlert para que no se pisen.
            setTimeout(() => {
                if (!result.status) {
                    Swal.fire('Error', result.msg, 'error');
                    return;
                }

                if (result.es_anomalia) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cierre marcado para revisión',
                        html: `Diferencia: Bs. ${result.diferencia}<br><small>${result.motivo}</small>`
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Caja cerrada', 'El cierre no presenta irregularidades.', 'success')
                        .then(() => location.reload());
                }
            }, 350);
        })
        .catch(error => {
            console.error('Error al cerrar caja:', error);
            Swal.fire('Error', 'No se pudo procesar el cierre. Revisa la consola.', 'error');
        });
}

function reentrenarModelo() {
    Swal.fire({
        title: 'Reentrenar modelo de IA',
        text: 'Se usará el historial completo de cierres de caja para actualizar el modelo de detección de anomalías.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, reentrenar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(base_url + '/Caja/reentrenarModelo', { method: 'POST' })
                .then(response => response.json())
                .then(result => {
                    result.status
                        ? Swal.fire('Listo', result.msg, 'success')
                        : Swal.fire('No se pudo reentrenar', result.msg, 'error');
                });
        }
    });
}