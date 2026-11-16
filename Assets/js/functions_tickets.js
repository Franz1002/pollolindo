// Pantalla Tickets: una fila por ticket, filtrada por fechas, con anulación de ventas
var tableTickets;

document.addEventListener('DOMContentLoaded', function () {
    const desde = document.getElementById('tk-desde');
    const hasta = document.getElementById('tk-hasta');
    const resumen = document.getElementById('tk-resumen');
    const aviso = document.getElementById('tk-aviso');

    // Los textos vienen de la base: se escapan antes de mostrarlos
    const texto = $.fn.dataTable.render.text().display;
    const dinero = (n) => (parseFloat(n) || 0).toFixed(2);
    const esAnulada = (ticket) => ticket.estado === 'anulada';
    // "2026-11-15 22:10:25" se muestra como "15/11 22:10"
    const fechaCorta = function (fecha) {
        const partes = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/.exec(String(fecha || ''));
        return partes ? partes[3] + '/' + partes[2] + ' ' + partes[4] + ':' + partes[5] : String(fecha || '');
    };
    // Cuenta letras: un punto o unos espacios no valen como motivo
    const letras = (cadena) => (String(cadena).match(/\p{L}/gu) || []).length;

    tableTickets = $('#tableTickets').DataTable({
        ajax: {
            url: base_url + '/Tickets/getTickets',
            data: function (parametros) {
                parametros.desde = desde.value;
                parametros.hasta = hasta.value;
            },
            dataSrc: function (respuesta) {
                aviso.textContent = respuesta.status ? '' : respuesta.msg;
                const filas = respuesta.status ? respuesta.data : [];
                // Las ventas anuladas se muestran, pero no suman en el total
                const vigentes = filas.filter((t) => !esAnulada(t));
                const anuladas = filas.length - vigentes.length;
                const total = vigentes.reduce((suma, t) => suma + (parseFloat(t.total) || 0), 0);
                resumen.textContent = vigentes.length + (vigentes.length === 1 ? ' ticket' : ' tickets')
                    + ' · ' + dinero(total) + ' Bs'
                    + (anuladas ? ' · ' + anuladas + (anuladas === 1 ? ' anulada' : ' anuladas') : '');
                return filas;
            }
        },
        // En cada columna, "display" es lo que se ve; para ordenar se usa el dato sin formato
        columns: [
            { data: 'numero_pedido', className: 'sin-corte', render: (n, modo) => modo === 'display' ? '#' + texto(n) : n },
            {
                data: 'fecha_emision',
                className: 'sin-corte',
                render: (f, modo) => (modo === 'display' || modo === 'filter') ? texto(fechaCorta(f)) : f
            },
            {
                data: 'productos',
                render: function (productos, tipo, fila) {
                    let salida = texto(productos || 'Sin detalle');
                    if (fila.nota_pedido) {
                        salida += '<br><small class="text-muted">Nota: ' + texto(fila.nota_pedido) + '</small>';
                    }
                    return salida;
                }
            },
            {
                data: 'tipo_pedido',
                render: function (tipo, modo, fila) {
                    if (tipo === 'llevar') return 'Para llevar';
                    if (tipo === 'mesa' && fila.mesa) return 'Mesa ' + texto(fila.mesa);
                    // Tickets antiguos: se anotaba un nombre en lugar de la mesa
                    return fila.nombre_cliente ? texto(fila.nombre_cliente) : '—';
                }
            },
            {
                data: 'metodo_pago',
                className: 'sin-corte',
                render: function (metodo) {
                    if (metodo === 'qr') return '<span class="badge badge-warning">QR</span>';
                    if (metodo === 'mixto') return '<span class="badge badge-warning">Mixto</span>';
                    return '<span class="badge badge-info">Efectivo</span>';
                }
            },
            {
                // El descuento es poco frecuente: va en pequeño bajo el total, sin columna propia
                data: 'total',
                className: 'text-right sin-corte',
                render: function (total, modo, fila) {
                    if (modo !== 'display') return parseFloat(total) || 0;
                    const descuento = parseFloat(fila.descuento) || 0;
                    return dinero(total)
                        + (descuento > 0 ? '<br><small class="text-muted">desc. ' + descuento + ' %</small>' : '');
                }
            },
            { data: 'cajero', render: (c) => texto(c || '—') },
            {
                // Estado de la venta: anulada (con su motivo), o el botón para anularla
                data: 'estado',
                orderable: false,
                className: 'sin-tachar',
                render: function (estado, modo, fila) {
                    if (estado === 'anulada') {
                        return '<span class="badge badge-danger">Anulada</span>'
                            + '<br><small>' + texto(fila.motivo_anulacion || 'Sin motivo') + '</small>'
                            + '<br><small class="text-muted">' + texto(fila.anulada_por_nombre || '—')
                            + ' · ' + texto(fechaCorta(fila.fecha_anulacion)) + '</small>';
                    }
                    if (parseInt(fila.puede_anular, 10) === 1) {
                        return '<button type="button" class="btn btn-outline-danger btn-sm tk-anular">Anular</button>';
                    }
                    return '';
                }
            }
        ],
        // Las ventas anuladas se ven tachadas
        createdRow: function (fila, datos) {
            if (esAnulada(datos)) fila.classList.add('fila-anulada');
        },
        order: [],          // se respeta el orden del servidor: lo más reciente primero
        autoWidth: false,   // el ancho lo reparte el navegador: Productos se queda con el espacio libre
        pageLength: 25,
        destroy: true
    });

    function recargar() {
        // Si "hasta" quedó antes que "desde", se igualan
        if (hasta.value < desde.value) hasta.value = desde.value;
        tableTickets.ajax.reload();
    }
    desde.addEventListener('change', recargar);
    hasta.addEventListener('change', recargar);
    document.getElementById('tk-hoy').addEventListener('click', function () {
        desde.value = this.dataset.hoy;
        hasta.value = this.dataset.hoy;
        recargar();
    });

    // ---------- Anular una venta ----------
    $('#tableTickets tbody').on('click', '.tk-anular', function () {
        const ticket = tableTickets.row(this.closest('tr')).data();
        if (ticket) pedirAnulacion(ticket);
    });

    function pedirAnulacion(ticket) {
        Swal.fire({
            titleText: '¿Anular el pedido #' + ticket.numero_pedido + '?',
            text: 'Total: ' + dinero(ticket.total) + ' Bs. La venta no se borra: queda registrada como anulada y deja de contar en la caja.',
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Motivo de la anulación (obligatorio)',
            inputAttributes: { maxlength: '200', 'aria-label': 'Motivo de la anulación' },
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'No anular',
            inputValidator: function (motivo) {
                return letras(motivo) < 5 ? 'Escribe el motivo (al menos 5 letras).' : undefined;
            }
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;

            const datos = new FormData();
            datos.append('id_ticket', ticket.id_ticket);
            datos.append('motivo', String(resultado.value).trim());

            fetch(base_url + '/Tickets/anularVenta', { method: 'POST', body: datos })
                .then((respuesta) => respuesta.json())
                .then(function (respuesta) {
                    Swal.fire(
                        respuesta.status ? 'Venta anulada' : 'No se anuló',
                        respuesta.msg,
                        respuesta.status ? 'success' : 'warning'
                    );
                })
                .catch(function () {
                    Swal.fire('Error', 'No hubo respuesta del sistema. Revisa en la lista si la venta quedó anulada.', 'error');
                })
                .finally(function () {
                    // Se vuelve a leer la lista sin cambiar de página
                    tableTickets.ajax.reload(null, false);
                });
        });
    }
});
