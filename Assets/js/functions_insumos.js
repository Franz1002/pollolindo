var tableInsumos;

document.addEventListener('DOMContentLoaded', function () {
    tableInsumos = $('#tableInsumos').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": base_url + "/Insumos/getInsumos",
            "dataSrc": "",
            "error": function (xhr) {
                console.log('Error cargando datos:', xhr.responseText);
            }
        },
        "colReorder": true,
        "headerCallback": function (thead) {
            $(thead).find('th').css({ 'color': '#c1c3ce' });
        },
        "columns": [
            { data: "id_insumo" },
            { data: "nombre_insumo" },
            { data: "tipo_insumo" },
            { data: "unidad_medida" },
            { data: "estado_texto" },
            { data: "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[1, "asc"]]
    });
});

function nuevoInsumo() {
    document.getElementById('formInsumo').reset();
    document.getElementById('idInsumo').value = "";
    document.getElementById('tituloInsumoModal').innerText = "Nuevo insumo";
    document.getElementById('filaEstadoInsumo').style.display = "none";

    const modal = new bootstrap.Modal(document.getElementById("modalFormInsumo"));
    modal.show();
}

function editInsumo(idInsumo) {
    fetch(base_url + '/Insumos/getInsumo/' + idInsumo)
        .then(response => response.json())
        .then(data => {
            const insumo = data[0];
            document.getElementById('idInsumo').value = insumo.id_insumo;
            document.getElementById('nombreInsumo').value = insumo.nombre_insumo;
            document.getElementById('tipoInsumo').value = insumo.tipo_insumo;
            document.getElementById('unidadInsumo').value = insumo.unidad_medida;
            document.getElementById('estadoInsumo').value = insumo.estado;
            document.getElementById('tituloInsumoModal').innerText = "Editar insumo";
            document.getElementById('filaEstadoInsumo').style.display = "block";

            const modal = new bootstrap.Modal(document.getElementById("modalFormInsumo"));
            modal.show();
        });
}

function guardarInsumo(e) {
    e.preventDefault();

    const idInsumo = document.getElementById('idInsumo').value;
    const url = idInsumo ? '/Insumos/actualizarInsumo' : '/Insumos/registrarInsumo';
    const formData = new FormData(document.getElementById('formInsumo'));

    fetch(base_url + url, { method: 'POST', body: formData })
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
            Swal.fire('Error', 'Hubo un problema al guardar el insumo.', 'error');
        });
}

function eliminarInsumo(idInsumo) {
    Swal.fire({
        title: '¿Desactivar este insumo?',
        text: "Ya no aparecerá disponible para nuevas recetas, pero no se borra su historial.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id_insumo', idInsumo);

            fetch(base_url + '/Insumos/eliminarInsumo', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(result => {
                    if (result.status) {
                        Swal.fire('¡Listo!', result.msg, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', result.msg, 'error');
                    }
                });
        }
    });
}
