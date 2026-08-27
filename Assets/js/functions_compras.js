document.addEventListener('DOMContentLoaded', function () {
    // Fecha de hoy por defecto
    document.getElementById('fechaCompra').value = new Date().toISOString().split('T')[0];

    // Carga los insumos activos en el dropdown
    fetch(base_url + '/Compras/getInsumosActivos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('insumoCompra');
            data.forEach(function (insumo) {
                const option = document.createElement('option');
                option.value = insumo.id_insumo;
                option.text = insumo.nombre_insumo + ' (' + insumo.unidad_medida + ')';
                option.dataset.stock = insumo.stock_actual;
                option.dataset.costo = insumo.costo_promedio;
                select.appendChild(option);
            });
        });

    document.getElementById('insumoCompra').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        document.getElementById('stockActualInfo').innerText =
            'Stock actual: ' + opt.dataset.stock + ' | Costo promedio actual: Bs. ' + opt.dataset.costo;
    });
});

function registrarCompra(e) {
    e.preventDefault();

    const formData = new FormData(document.getElementById('formCompra'));

    fetch(base_url + '/Compras/registrarCompra', {
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
            Swal.fire('Error', 'Hubo un problema al registrar la compra.', 'error');
        });
}
