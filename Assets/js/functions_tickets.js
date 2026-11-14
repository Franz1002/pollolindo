// Pantalla Tickets: una fila por ticket, filtrada por fechas
var tableTickets;

document.addEventListener('DOMContentLoaded', function () {
    const desde = document.getElementById('tk-desde');
    const hasta = document.getElementById('tk-hasta');
    const resumen = document.getElementById('tk-resumen');
    const aviso = document.getElementById('tk-aviso');

    // Los textos vienen de la base: se escapan antes de mostrarlos
    const texto = $.fn.dataTable.render.text().display;
    const dinero = (n) => (parseFloat(n) || 0).toFixed(2);

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
                const total = filas.reduce((suma, t) => suma + (parseFloat(t.total) || 0), 0);
                resumen.textContent = filas.length + (filas.length === 1 ? ' ticket' : ' tickets') + ' · ' + dinero(total) + ' Bs';
                return filas;
            }
        },
        columns: [
            { data: 'numero_pedido', render: (n) => '#' + texto(n) },
            { data: 'fecha_emision', render: (f) => texto(String(f).slice(0, 16)) },
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
                render: function (metodo) {
                    if (metodo === 'qr') return '<span class="badge badge-warning">QR</span>';
                    if (metodo === 'mixto') return '<span class="badge badge-warning">Mixto</span>';
                    return '<span class="badge badge-info">Efectivo</span>';
                }
            },
            {
                data: 'descuento',
                render: (d) => parseFloat(d) > 0 ? parseFloat(d) + ' %' : '—'
            },
            { data: 'total', className: 'text-right', render: (t) => dinero(t) },
            { data: 'cajero', render: (c) => texto(c || '—') }
        ],
        order: [],          // se respeta el orden del servidor: lo más reciente primero
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
});
