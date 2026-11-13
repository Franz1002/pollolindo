document.addEventListener('DOMContentLoaded', function () {
    $('#tableCompras').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": base_url + "/Compras/getCompras",
            "dataSrc": function (json) {
                // Calcula el costo unitario de cada compra para mostrarlo en la tabla
                json.forEach(function (fila) {
                    fila.costo_unitario = (fila.costo_total / fila.cantidad).toFixed(2);
                });
                return json;
            },
            "error": function (xhr) {
                console.log('Error cargando datos:', xhr.responseText);
            }
        },
        "colReorder": true,
        "headerCallback": function (thead) {
            $(thead).find('th').css({ 'color': '#c1c3ce' });
        },
        "columns": [
            { data: "id_compra" },
            { data: "nombre_insumo" },
            { data: "cantidad" },
            { data: "costo_total" },
            { data: "costo_unitario" },
            { data: "fecha_compra" },
            { data: "nombre_usuario" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[0, "desc"]]
    });
});
