var tableUsuarios;
var modalUsuario;
var modalVerUsuario;

document.addEventListener('DOMContentLoaded', function () {
    // Una sola instancia de cada ventana: así se pueden abrir y cerrar sin recargar la página
    modalUsuario = new bootstrap.Modal(document.getElementById('modalFormUsuario'));
    modalVerUsuario = new bootstrap.Modal(document.getElementById('modalVerUsuario'));

    tableUsuarios = $('#tableUsuarios').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "ajax": {
            "url": base_url + "/Usuarios/getUsuarios",
            "dataSrc": "",
            "error": function (xhr) {
                console.log('Error cargando datos:', xhr.responseText);
            }
        },
        "headerCallback": function (thead) {
            $(thead).find('th').css({ 'color': '#c1c3ce' });
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

// Prepara el formulario en modo "nuevo" o "editar"
function prepararFormulario(esEdicion) {
    document.getElementById('formUsuario').reset();
    document.getElementById('idUsuario').value = '';
    document.getElementById('modalFormUsuarioLabel').textContent = esEdicion ? 'Actualizar usuario' : 'Nuevo usuario';
    document.getElementById('btnText').textContent = esEdicion ? ' Actualizar' : ' Registrar';

    const password = document.getElementById('password');
    password.value = '';
    password.required = !esEdicion;
    password.placeholder = esEdicion ? 'Déjala vacía para no cambiarla' : 'Mínimo 6 caracteres';
}

async function cargarCargos() {
    const response = await fetch(base_url + '/Usuarios/getSelectCargos');
    document.getElementById('listCargo').innerHTML = await response.text();
}

async function openModalUsuario() {
    prepararFormulario(false);
    await cargarCargos();
    modalUsuario.show();
}

async function editUser(id) {
    prepararFormulario(true);
    await cargarCargos();

    const response = await fetch(base_url + '/Usuarios/getUsuario/' + id);
    const result = await response.json();
    if (!result.status) {
        Swal.fire('Error', result.msg, 'error');
        return;
    }

    const u = result.data;
    document.getElementById('idUsuario').value = u.id;
    document.getElementById('nombres').value = u.nombre;
    document.getElementById('apellidos').value = u.apellido;
    document.getElementById('ci').value = u.cedula;
    document.getElementById('celular').value = u.celular;
    document.getElementById('email').value = u.email;
    document.getElementById('listCargo').value = String(u.tipo);
    document.getElementById('estado').value = String(u.estado);
    modalUsuario.show();
}

async function registrarUsuario(e) {
    e.preventDefault();

    const esEdicion = document.getElementById('idUsuario').value !== '';
    const obligatorios = ['nombres', 'apellidos', 'ci', 'celular', 'email', 'listCargo', 'estado'];
    if (!esEdicion) {
        obligatorios.push('password');
    }
    const faltaAlguno = obligatorios.some(id => document.getElementById(id).value.trim() === '');
    if (faltaAlguno) {
        Swal.fire('Atención', 'Completa todos los campos obligatorios.', 'warning');
        return;
    }

    try {
        const response = await fetch(base_url + '/Usuarios/registrarUsuario', {
            method: 'POST',
            body: new FormData(document.getElementById('formUsuario'))
        });
        const result = await response.json();

        if (result.status) {
            modalUsuario.hide();
            tableUsuarios.api().ajax.reload();
            Swal.fire({ icon: 'success', title: esEdicion ? 'Actualizado' : 'Registrado', text: result.msg, timer: 2500 });
        } else {
            Swal.fire('Atención', result.msg, 'warning');
        }
    } catch (error) {
        console.error('Error al guardar el usuario:', error);
        Swal.fire('Error', 'No se pudo guardar el usuario. Inténtalo de nuevo.', 'error');
    }
}

async function viewUser(id) {
    const response = await fetch(base_url + '/Usuarios/getUsuario/' + id);
    const result = await response.json();
    if (!result.status) {
        Swal.fire('Error', result.msg, 'error');
        return;
    }

    const u = result.data;
    // textContent muestra el texto tal cual, sin interpretarlo como HTML
    document.getElementById('verNombre').textContent = u.nombre;
    document.getElementById('verApellido').textContent = u.apellido;
    document.getElementById('verEmail').textContent = u.email;
    document.getElementById('verCedula').textContent = u.cedula;
    document.getElementById('verCelular').textContent = u.celular;
    document.getElementById('verUsuario').textContent = u.nombre_tipo;
    modalVerUsuario.show();
}

function deletUser(id) {
    Swal.fire({
        title: 'Dar de baja al usuario',
        text: 'Ya no podrá entrar al sistema, pero sus ventas y cierres de caja se conservan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5B77F5',
        cancelButtonColor: '#FF7272',
        confirmButtonText: 'Sí, dar de baja',
        cancelButtonText: 'No, cancelar'
    }).then(async (respuesta) => {
        if (!respuesta.isConfirmed) {
            return;
        }
        const datos = new FormData();
        datos.append('idUsuario', id);
        try {
            const response = await fetch(base_url + '/Usuarios/deleteUsuario', { method: 'POST', body: datos });
            const result = await response.json();
            if (result.status) {
                tableUsuarios.api().ajax.reload();
                Swal.fire({ icon: 'success', title: 'Listo', text: result.msg, timer: 2500 });
            } else {
                Swal.fire('Error', result.msg, 'error');
            }
        } catch (error) {
            console.error('Error al dar de baja al usuario:', error);
            Swal.fire('Error', 'No se pudo dar de baja al usuario.', 'error');
        }
    });
}