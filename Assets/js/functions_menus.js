// Función para cargar categorías (usada en ambos casos)
function cargarCategorias() {
    $.ajax({
        url: base_url + "/Categorias/getCategorias",
        type: "GET",
        dataType: "json",
        success: function (data) {
            const select = document.getElementById("categoria_id");
            select.innerHTML = '<option disabled selected>Seleccione</option>';
            data.forEach(categoria => {
                const option = document.createElement("option");
                option.value = categoria.id_categoria;
                option.textContent = categoria.nombre_categoria;
                select.appendChild(option);
            });
        },
        error: function () {
            console.error("Error al cargar categorías.");
        }
    });
}

// Abrir modal para registrar nuevo menú
function openModalMenu() {
    document.getElementById("formMenu").reset();
    document.getElementById("idMenu").value = "";
    document.getElementById("btnText").textContent = "Registrar";

    // Limpiar vista previa de imagen
    const imgPreview = document.getElementById("imgPreview");
    imgPreview.src = "";
    imgPreview.style.display = "none";

    cargarCategorias();

    const modal = new bootstrap.Modal(document.getElementById("modalFormMenu"));
    modal.show();
}

// Llamar a la función para cargar las categorías
$(document).ready(function () {
    cargarCategorias();
});

// Vista previa cuando se selecciona imagen nueva
document.getElementById("img_menu").addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imgPreview = document.getElementById("imgPreview");
            imgPreview.src = e.target.result;
            imgPreview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});

// Función para registrar menú
function registrarMenu(e) {
    e.preventDefault();

    // Verificar si los elementos existen
    const strNombre = document.getElementById("nombre_menu");
    const strCategoria = document.getElementById("categoria_id");
    const strDetalle = document.getElementById("detalle_menu");
    const fotoMenu = document.getElementById("img_menu");
    const estadoMenu = document.getElementById("estado");

    if (!strNombre || !strCategoria || !strDetalle || !fotoMenu || !estadoMenu) {
        console.error("Uno o más elementos no se encontraron en el DOM.");
        return;
    }

    const nombreMenu = strNombre.value;
    const categoriaMenu = strCategoria.value;
    const detalleMenu = strDetalle.value;
    const imgMenu = fotoMenu.files[0];
    const estado = estadoMenu.value;

    // Validación de campos
    if (nombreMenu == "" || categoriaMenu == "" || detalleMenu == "") {
        Swal.fire({
            icon: 'error',
            showCloseButton: true,
            title: '<span style="color: #ff4d4d; font-size: 32px; font-weight: bold;">¡ATENCIÓN!</span>',
            html: `<div style="font-size: 28px; color: #707070; margin-top: 10px;">
                    <p style="margin: 0;">Los campos con asterisco son obligatorios</p>
                   </div>`,
            timer: 7000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-alert-popup',
                title: 'custom-alert-title',
            },
            width: '400px',
            padding: '20px',
            background: '#ffffff',
            backdrop: `rgba(255, 0, 96, 0.2) left top no-repeat`
        });
    } else {
        // Usar FormData para enviar los datos junto con la imagen
        const form = document.getElementById("formMenu");
        const formData = new FormData(form);
        formData.set('categoria_id', strCategoria.value);

        fetch(base_url + '/Menus/registrarMenu', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Menú registrado!',
                    text: 'El menú ha sido registrado exitosamente.',
                }).then(() => {
                    // Recargar la tabla sin cambiar la página
                    tableMenus.ajax.reload(null, false);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Hubo un error al registrar el menú.',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un problema al registrar el menú.',
            });
        });
    }
}

// Función para editar menú existente
function editMenu(idMenu) {
    $.ajax({
        url: base_url + "/Menus/getMenu/" + idMenu,
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status) {
                const menu = response.data[0];

                $('#idMenu').val(menu.id_menu);
                $('#nombre_menu').val(menu.nombre_menu);
                $('#precio_menu').val(menu.precio_menu);
                $('#detalle_menu').val(menu.detalle_menu);
                $('#estado').val(menu.estado);
                $('#imgActual').val(menu.img_menu); // Guarda nombre original

                // Mostrar imagen
                const imgPreview = document.getElementById("imgPreview");
                imgPreview.src = base_url + "/Assets/images/imgMenu/" + menu.img_menu;
                imgPreview.style.display = "block";

                cargarCategorias();
                setTimeout(() => {
                    $('#categoria_id').val(menu.categoria_id);
                }, 300);

                document.getElementById("btnText").textContent = "Actualizar";

                const modal = new bootstrap.Modal(document.getElementById("modalFormMenu"));
                modal.show();
            }
        },
        error: function () {
            console.log("Error al obtener los datos del menú.");
        }
    });
}

// Función para actualizar menú
function actualizarMenu(e) {
    e.preventDefault();

    const form = document.getElementById("formMenu");
    const formData = new FormData(form);
    const idMenu = document.querySelector('#idMenu').value;

    if (!idMenu) {
        alert("El ID del menú es necesario para actualizar.");
        return;
    }

    // Realiza la petición de actualización
    fetch(base_url + '/Menus/actualizarMenu', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status) {
            Swal.fire({
                icon: 'success',
                title: '¡Menú actualizado!',
                text: 'El menú ha sido actualizado exitosamente.',
            }).then(() => {
                // Recargar la tabla sin cambiar la página
                tableMenus.ajax.reload(null, false);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un error al actualizar el menú.',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'Hubo un problema al actualizar el menú.',
        });
    });
}

// Inicialización del DataTable para mostrar los menús
$(document).ready(function () {
    tableMenus = $('#tableMenus').DataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "ajax": {
            "url": base_url + "/Menus/getMenus",
            "dataSrc": "",
            "error": function (xhr, error, thrown) {
                console.log('Error cargando datos:', xhr.responseText);
            }
        },
        "columns": [
            { "data": "id_menu" },
            { "data": "nombre_menu" },
            { "data": "precio_menu" },
            { "data": "nombre_categoria" },
            { "data": "detalle_menu" },
            {
                "data": "img_menu",
                "render": function (data) {
                    return `<div style="max-width: 120px; overflow: hidden; display: flex; justify-content: center;">
                                <img src="${data}" style="width: 100%; height: auto; border-radius: 10px;">
                            </div>`;
                }
            },
            {
                "data": "estado_menu",
                "render": function (data) {
                    return data == 1 ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "order": [[0, "desc"]]
    });
});
