// Pantalla Configuración: guarda los ajustes del negocio
document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('formConfiguracion');
    const boton = document.getElementById('btnGuardarConfiguracion');

    formulario.addEventListener('submit', function (evento) {
        evento.preventDefault();
        boton.disabled = true;

        fetch(base_url + '/Configuracion/guardarConfiguracion', {
            method: 'POST',
            body: new FormData(formulario)
        })
            .then((respuesta) => respuesta.json())
            .then((datos) => {
                Swal.fire(datos.status ? 'Guardado' : 'Revisa el dato', datos.msg, datos.status ? 'success' : 'warning');
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo guardar la configuración. Inténtalo de nuevo.', 'error');
            })
            .finally(() => {
                boton.disabled = false;
            });
    });
});
