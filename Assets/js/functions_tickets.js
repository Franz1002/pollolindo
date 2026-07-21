var tableTickets;

document.addEventListener('DOMContentLoaded', function () {
    tableTickets = $('#tableTickets').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" // Ruta completa al archivo JSON
        },
        "ajax": {
            "url": base_url + "/Tickets/getTickets",
            "dataSrc": "",
            "error": function (xhr, error, thrown) {
                console.log('Error cargando datos:', xhr.responseText);
            }
        },
     
        "colReorder": true,
        "headerCallback": function (thead) {
            $(thead).find('th').css({
                'color': '#c1c3ce'
            });
        },

        "columns": [
            { data: "id_ticket" },
            { data: "numero_pedido" },
            { data: "nombre_usuario_completo" },
            { data: "fecha_emision" },
            { data: "descuento" },
            { data: "total_venta" },
            { data: "ticket" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[0, "asc"]]
    });
});