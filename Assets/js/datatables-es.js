// Textos en español para todas las tablas (DataTables).
// Van dentro del proyecto para que el sistema funcione sin internet.
$.extend(true, $.fn.dataTable.defaults, {
    language: {
        emptyTable: "No hay datos para mostrar",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        infoEmpty: "Mostrando 0 registros",
        infoFiltered: "(filtrado de _MAX_ registros en total)",
        lengthMenu: "Mostrar _MENU_ registros",
        loadingRecords: "Cargando...",
        processing: "Procesando...",
        search: "Buscar:",
        zeroRecords: "No se encontraron resultados",
        paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        },
        aria: {
            orderable: "Ordenar por esta columna",
            orderableReverse: "Invertir el orden de esta columna"
        }
    }
});
