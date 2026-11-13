var tableCategorias;

document.addEventListener('DOMContentLoaded', function () {
    tableCategorias = $('#tableCategorias').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": base_url + "/Categorias/getCategorias",
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
            { data: "id_categoria" },
            { data: "nombre_categoria" },
            { data: "descripcion_categoria" },
            { data: "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[0, "asc"]]
    });
});

function openModalCategoria() {
    // Limpiar el valor del campo oculto (idCategoria) y reiniciar el formulario
    document.querySelector('#idCategoria').value = "";
    document.querySelector("#formCategoria").reset();

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalFormCategoria'));
    modal.show();

}

// Función para registrar una categoría
function registrarCategoria(e) {
    e.preventDefault();

    const idCategoria = document.getElementById("idCategoria").value;
    if (idCategoria) {
        editarCategoria(e); // Si hay ID, llamamos a editar
        return;
    }

    const nombreCategoria = document.getElementById("nombre_categoria").value;
    const descripcionCategoria = document.getElementById("descripcion_categoria").value;

    if (nombreCategoria === "" || descripcionCategoria === "") {
        Swal.fire({ icon: 'error', title: '¡Atención!', text: 'Todos los campos son obligatorios.' });
        return;
    }

    const formData = new FormData();
    formData.append("nombre", nombreCategoria);
    formData.append("descripcion", descripcionCategoria);

    fetch(base_url + '/Categorias/registrarCategoria', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                Swal.fire({ icon: 'success', title: '¡Categoría registrada!', text: 'Se registró correctamente.' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: '¡Error!', text: 'No se pudo registrar.' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un problema con el registro.', 'error');
        });
}


function editCategory(id_categoria) {
    fetch(base_url + '/Categorias/getCategoria/' + id_categoria)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                document.getElementById("idCategoria").value = data.data.id_categoria;
                document.getElementById("nombre_categoria").value = data.data.nombre_categoria;
                document.getElementById("descripcion_categoria").value = data.data.descripcion_categoria;

                document.getElementById("titleModal").innerText = "Editar Categoría";
                document.getElementById("btnText").innerText = "Actualizar";
                document.getElementById("btnActionForm").setAttribute("onclick", "editarCategoria(event)");

                // Mostrar el modal usando JavaScript puro (Bootstrap 5)
                let modal = new bootstrap.Modal(document.getElementById('modalFormCategoria'));
                modal.show();
            } else {
                Swal.fire('Error', 'No se pudo cargar la categoría', 'error');
            }
        })
        .catch(error => console.error('Error:', error));
}



function editarCategoria(e) {
    e.preventDefault();

    const idCategoria = document.getElementById("idCategoria").value;
    const nombreCategoria = document.getElementById("nombre_categoria").value;
    const descripcionCategoria = document.getElementById("descripcion_categoria").value;

    if (nombreCategoria === "" || descripcionCategoria === "") {
        Swal.fire({ icon: 'error', title: '¡Atención!', text: 'Todos los campos son obligatorios.' });
        return;
    }

    const formData = new FormData();
    formData.append("id_categoria", idCategoria);
    formData.append("nombre", nombreCategoria);
    formData.append("descripcion", descripcionCategoria);

    fetch(base_url + '/Categorias/editCategoria', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Categoría actualizada correctamente.' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: '¡Error!', text: 'No se pudo actualizar.' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un problema con la actualización.', 'error');
        });
}


