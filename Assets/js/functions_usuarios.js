var tableUsuarios;

document.addEventListener('DOMContentLoaded', function () {
    tableUsuarios = $('#tableUsuarios').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" // Ruta completa al archivo JSON
        },
        "ajax": {
            "url": base_url + "/Usuarios/getUsuarios",
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
            { "data": "id" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "cedula" },
            { "data": "email" },
            { "data": "celular" },
            { "data": "tipo" },
            { "data": "estado" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "order": [[0, "asc"]]
    });
});

function openModalUsuario() {
    document.querySelector('#idUsuario').value = "";
    document.querySelector("#formUsuario").reset();
    const modal = new bootstrap.Modal(document.getElementById('modalFormUsuario'));
    modal.show();
    fntCargosUsuario(); // Cargar los cargos cuando se abra el modal
}

function fntCargosUsuario() {
    if (document.querySelector('#listCargo')) {
        const ajaxUrl = base_url + '/Usuarios/getSelectCargos';  // Verifica si esta URL está correcta.
        fetch(ajaxUrl)
            .then(response => response.text())
            .then(data => {
                document.querySelector('#listCargo').innerHTML = data;
                $('#listCargo').selectpicker('render');
            })
            .catch(error => console.error('Error:', error));
    }
}




function registrarUsuario(e) {
    e.preventDefault();

    const intIdUsuario = document.querySelector('#idUsuario').value;
    const strNombres = document.getElementById("nombres");
    const strApellidos = document.getElementById("apellidos");
    const strCedula = document.getElementById("ci");
    const intCelular = document.getElementById("celular");
    const strEmail = document.getElementById("email");
    const strPassword = document.getElementById("password");
    const intTipoUsuario = document.getElementById("listCargo"); // Corregido
    const intEstado = document.getElementById("estado");

    // Validación de los campos del formulario
    if (strNombres.value == "" || strApellidos.value == "" || strCedula.value == "" || intCelular.value == "" || strEmail.value == "" || strPassword.value == "" || intTipoUsuario.value == "" || intEstado.value == "") {
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
        const form = document.getElementById("formUsuario");
        const formData = new FormData(form);

        // Usando fetch para el envío de la solicitud AJAX
        const ajaxUrl = base_url + '/Usuarios/registrarUsuario';
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(result => {
                if (result.status) {
                    Swal.fire({
                        icon: 'success',
                        title: '<span style="font-size: 24px; font-weight: bold; text-decoration: underline;">REGISTRADO</span>',
                        html: '<p style="font-size: 18px;">¡Usuario registrado exitosamente!</p>',
                        showConfirmButton: true,
                        timer: 3000,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar',
                        background: '#f9f9f9',
                        customClass: {
                            popup: 'custom-alert-popup',
                            title: 'custom-alert-title',
                            confirmButton: 'custom-alert-button'
                        },
                        width: '500px',
                        height: 'auto',
                        padding: '20px',

                    }).then(() => {
                        // Recargar la página después de que se cierre el mensaje de éxito
                        location.reload(); // Esto recarga la página
                    });
                    const modal = new bootstrap.Modal(document.getElementById('modalFormUsuario'));
                    modal.hide();
                    formUsuario.reset();
                    tableUsuarios.api().ajax.reload();
                } else if (result.statuss) {
                    Swal.fire({
                        icon: 'success',
                        title: '<span style="font-size: 24px; font-weight: bold; text-decoration: underline;">EDITADO</span>',
                        html: '<p style="font-size: 18px;">¡Usuario editado exitosamente!</p>',
                        showConfirmButton: true,
                        timer: 4000,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar',
                        background: '#f9f9f9',
                        customClass: {
                            popup: 'custom-alert-popup',
                            title: 'custom-alert-title',
                            confirmButton: 'custom-alert-button'
                        },
                        width: '500px', // Cambia el ancho
                        height: 'auto', // Cambia la altura
                        padding: '20px', // Añade espaciado interno
                    });
                    $('#modalFormUsuario').modal("hide");
                    formUsuario.reset();
                    tableUsuarios.api().ajax.reload();
                } else if (result.statusss) {
                    Swal.fire({
                        icon: 'error',
                        title: '<span style="font-size: 24px; font-weight: bold; text-decoration: underline;">Error</span>',
                        html: '<p style="font-size: 18px;">El Usuario ya existe</p>',
                        showConfirmButton: true,
                        timer: 4000,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Aceptar',
                        background: '#f9f9f9',
                        customClass: {
                            popup: 'custom-alert-popup',
                            title: 'custom-alert-title',
                            confirmButton: 'custom-alert-button'
                        },
                        width: '400px', // Cambia el ancho
                        height: 'auto', // Cambia la altura
                        padding: '20px', // Añade espaciado interno
                    });
                }
            })
            .catch(error => console.error('Error al registrar usuario:', error));
    }
}

function viewUser(id) {
    const request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    const ajaxUrl = base_url + '/Usuarios/getUsuario/' + id;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            const objData = JSON.parse(request.responseText);

            if (objData.status) {
                document.querySelector("#verNombre").innerHTML = objData.data.nombre;
                document.querySelector("#verApellido").innerHTML = objData.data.apellido;
                document.querySelector("#verEmail").innerHTML = objData.data.email;
                document.querySelector("#verCedula").innerHTML = objData.data.cedula;
                document.querySelector("#verCelular").innerHTML = objData.data.celular;
                document.querySelector("#verUsuario").innerHTML = objData.data.nombre_tipo;
                $('#modalVerUsuario').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}


function editUser(id) {
    document.getElementById("modalTitle").innerHTML = "Actualizar usuario";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.getElementById("btnText").innerHTML = " Actualizar";
    const xhttp = new XMLHttpRequest();
    const ajaxUrl = base_url + '/Usuarios/getUsuario/' + id;
    xhttp.open("GET", ajaxUrl, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            const result = JSON.parse(xhttp.responseText);
            if (result.status) {
                document.getElementById("idUsuario").value = result.data.id;
                document.getElementById("apellidos").value = result.data.apellido;
                document.getElementById("nombres").value = result.data.nombre;
                document.getElementById("ci").value = result.data.cedula;
                document.getElementById("celular").value = result.data.celular;
                document.getElementById("email").value = result.data.email;
                document.getElementById("password").value = result.data.password;
                document.getElementById("cargo").value = result.data.tipo.toString();
                document.getElementById("estado").value = result.data.estado.toString();


                $('#modalFormUsuario').modal('show');
            } else {
                swal("Error", result.msg, "error");
            }
        }
    }
}

function deletUser(id) {
    Swal.fire({
        title: '<span style="font-size: 24px; font-weight: bold;">Eliminar Usuario</span>',
        html: '<p style="font-size: 18px;">Confirma si deseas eliminar al Usuario</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5B77F5',
        cancelButtonColor: '#FF7272',
        confirmButtonText: '<span style="font-size: 18px;">Si, eliminar</span>',
        cancelButtonText: '<span style="font-size: 18px;">No, cancelar</span>',
        customClass: {
            popup: 'custom-alert-popup',
            title: 'custom-alert-title',
            content: 'custom-alert-content',
            cancelButton: 'custom-alert-button',
            confirmButton: 'custom-alert-button'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const ajaxUrl = base_url + '/Usuarios/deleteUsuario/';
            const strData = "idUsuario=" + id;
            const xhttp = new XMLHttpRequest();

            xhttp.open("POST", ajaxUrl, true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(strData);
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    const result = JSON.parse(xhttp.responseText);
                    if (result.status) {
                        Swal.fire({
                            icon: 'success',
                            title: '<span style="font-size: 24px; font-weight: bold;">¡Éxito!</span>',
                            html: '<p style="font-size: 18px;">Usuario eliminado con éxito.</p>',
                            timer: 3000,
                            timerProgressBar: true,
                            confirmButtonColor: '#28a745',
                            background: '#f9f9f9',
                            customClass: {
                                popup: 'custom-alert-popup',
                                title: 'custom-alert-title',
                                confirmButton: 'custom-alert-button'
                            }
                        });
                        tableUsuarios.api().ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '<span style="font-size: 24px; font-weight: bold;">Error</span>',
                            html: '<p style="font-size: 18px;">' + result + '</p>',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK',
                            background: '#f9f9f9',
                            customClass: {
                                popup: 'custom-alert-popup',
                                title: 'custom-alert-title',
                                confirmButton: 'custom-alert-button'
                            }
                        });
                    }

                }
            }
        }
    })
}

