var tableRecetas;
var listaInsumos = [];

document.addEventListener('DOMContentLoaded', function () {
    tableRecetas = $('#tableRecetas').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "ajax": {
            "url": base_url + "/Recetas/getMenusReceta",
            "dataSrc": "",
            "error": function (xhr, error, thrown) {
                console.log('Error cargando datos:', xhr.responseText);
            }
        },
        "colReorder": true,
        "headerCallback": function (thead) {
            $(thead).find('th').css({ 'color': '#c1c3ce' });
        },
        "columns": [
            { data: "id_menu" },
            { data: "nombre_menu" },
            { data: "precio_menu" },
            { data: "estado_receta" },
            { data: "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[0, "asc"]]
    });

    // Carga la lista de insumos una sola vez para llenar los <select> del formulario
    fetch(base_url + '/Recetas/getInsumos')
        .then(response => response.json())
        .then(data => { listaInsumos = data; });
});

function crearFilaHTML(insumoIdSeleccionado, cantidad) {
    insumoIdSeleccionado = insumoIdSeleccionado || "";
    cantidad = cantidad || "";

    let opciones = '<option value="" disabled selected>Seleccione un insumo</option>';
    listaInsumos.forEach(function (insumo) {
        const selected = (insumo.id_insumo == insumoIdSeleccionado) ? 'selected' : '';
        opciones += `<option value="${insumo.id_insumo}" ${selected}>${insumo.nombre_insumo} (${insumo.unidad_medida})</option>`;
    });

    return `
        <div class="row filaReceta mb-2 align-items-center">
            <div class="col-7">
                <select class="form-control" name="insumo_id[]" required>${opciones}</select>
            </div>
            <div class="col-3">
                <input type="number" step="0.01" min="0" class="form-control" name="cantidad[]" placeholder="Cantidad" value="${cantidad}" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.filaReceta').remove()">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>`;
}

function agregarFilaReceta(insumoId, cantidad) {
    document.getElementById('filasReceta').insertAdjacentHTML('beforeend', crearFilaHTML(insumoId, cantidad));
}

function editarReceta(idMenu, nombreMenu) {
    document.getElementById('recetaIdMenu').value = idMenu;
    document.getElementById('tituloRecetaModal').innerText = 'Receta: ' + nombreMenu;
    document.getElementById('filasReceta').innerHTML = '';

    fetch(base_url + '/Recetas/getReceta/' + idMenu)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(function (fila) {
                    agregarFilaReceta(fila.insumo_id, fila.cantidad_usada);
                });
            } else {
                agregarFilaReceta(); // Empieza con una fila vacía si no tiene receta todavía
            }
            const modal = new bootstrap.Modal(document.getElementById('modalFormReceta'));
            modal.show();
        });
}

function guardarReceta(e) {
    e.preventDefault();

    const filas = document.querySelectorAll('.filaReceta');
    if (filas.length === 0) {
        Swal.fire({ icon: 'error', title: '¡Atención!', text: 'Agrega al menos un insumo.' });
        return;
    }

    const formData = new FormData(document.getElementById('formReceta'));

    fetch(base_url + '/Recetas/guardarReceta', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                Swal.fire({ icon: 'success', title: '¡Listo!', text: result.msg })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: '¡Error!', text: result.msg });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un problema al guardar la receta.', 'error');
        });
}
