let productos = [];
let subtotal = 0,
    totalFinal = 0;

// Agregar evento a todos los botones de productos (incluye menú, bebidas y combos)
document.querySelectorAll(".btn-product").forEach((button) => {
    button.addEventListener("click", function () {
        const price = parseFloat(this.dataset.price);
        const quantityInput = this.nextElementSibling;
        const quantity = parseInt(quantityInput.value);
        const name = this.textContent.split("-")[0].trim();

        agregarProducto(name, price, quantity);
    });
});

function agregarProducto(name, price, quantity) {
    // Verificar si el producto ya existe en la lista
    let productoExistente = productos.find((p) => p.name === name);

    if (productoExistente) {
        // Si ya existe, solo aumentamos la cantidad y recalculamos el total
        productoExistente.cantidad += quantity;
        productoExistente.total = productoExistente.cantidad * productoExistente.price;
    } else {
        // Si no existe, lo agregamos como nuevo
        productos.push({
            name,
            price,
            cantidad: quantity,
            total: price * quantity
        });
    }

    actualizarTicket();
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    actualizarTicket();
}

function actualizarTicket() {
    const lista = document.getElementById("productosSeleccionados");
    lista.innerHTML = "";

    subtotal = productos.reduce((sum, p) => sum + p.total, 0);
    totalFinal = subtotal;

    productos.forEach((p, index) => {
        let li = document.createElement("li");
        li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
        li.innerHTML = `${p.cantidad}x ${p.name} - $${p.total.toFixed(2)} 
    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">X</button>`;
        lista.appendChild(li);
    });

    // Actualizar los totales en la sección de Pagos
    document.getElementById("subtotal").textContent = subtotal.toFixed(2);
    document.getElementById("total-final").textContent = totalFinal.toFixed(2);
    calcularCambio();
}

function aplicarDescuento() {
    // Obtener el descuento en efectivo (monto fijo)
    const descuentoEfectivo = parseFloat(document.getElementById('descuento').value) || 0;

    // Calcular el total final restando el descuento en efectivo al subtotal
    totalFinal = subtotal - descuentoEfectivo;

    // Mostrar el total final en la interfaz
    document.getElementById('total-final').textContent = totalFinal.toFixed(2);
}

function calcularCambio() {
    let efectivo = parseFloat(document.getElementById('efectivo-recibido').value) || 0;
    let cambio = efectivo - totalFinal;
    if (cambio < 0) {
        document.getElementById('cambio').textContent = "Falta dinero";
    } else {
        document.getElementById('cambio').textContent = cambio.toFixed(2);
    }
}

let numeroPedido = 1; // Variable global para contar pedidos

function obtenerNumeroPedido() {
    return numeroPedido++;
}

function numeroALetras(num) {
    const unidades = ["", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE"];
    const especiales = ["DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISÉIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE"];
    const decenas = ["", "DIEZ", "VEINTE", "TREINTA", "CUARENTA", "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA"];
    const centenas = ["", "CIEN", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS"];




    function convertirEntero(n) {
        if (n === 0) return "CERO";
        if (n < 10) return unidades[n];
        if (n < 20) return especiales[n - 10];
        if (n < 100) return decenas[Math.floor(n / 10)] + (n % 10 !== 0 ? " Y " + unidades[n % 10] : "");
        if (n < 1000) return (n === 100 ? "CIEN" : centenas[Math.floor(n / 100)] + (n % 100 !== 0 ? " " + convertirEntero(n % 100) : ""));
        if (n < 1000000) return (Math.floor(n / 1000) === 1 ? "MIL" : convertirEntero(Math.floor(n / 1000)) + " MIL") + (n % 1000 !== 0 ? " " + convertirEntero(n % 1000) : "");
        if (n < 1000000000) return convertirEntero(Math.floor(n / 1000000)) + " MILLONES" + (n % 1000000 !== 0 ? " " + convertirEntero(n % 1000000) : "");
        return "NÚMERO FUERA DE RANGO";
    }

    // Separar parte entera y decimal
    let [entero, decimal] = num.toFixed(2).split(".");
    let resultado = convertirEntero(parseInt(entero)) + " CON " + decimal + "/100 BOLIVIANOS";

    return resultado;
}
function finalizarVenta() {
    if (productos.length === 0) {
        alert("No hay productos en la venta.");
        return;
    }

    let efectivo = parseFloat(document.getElementById("efectivo-recibido").value) || 0;
    let cambio = efectivo - totalFinal;

    // Generar ticket PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.setFont("courier", "bold");
    doc.setFontSize(14);
    doc.text("Chicken Lindo Srl.", 15, 10);
    doc.setFontSize(14);
    doc.text("Pollo Lindo.", 25, 15);
    doc.setFontSize(8);
    doc.text("Cochabamba - Bolivia", 24, 20);
    doc.text("NIT: 1009311029", 10, 25);
    doc.text(`FECHA: ${new Date().toLocaleDateString()}`, 10, 30);

    let cliente = document.getElementById("nombre-cliente").value.trim();
    cliente = cliente !== "" ? cliente : "CONSUMIDOR FINAL";
    doc.text(`CLIENTE: ${cliente}`, 10, 35);

    doc.text("=====================================", 10, 40);
    doc.text("Cant  Descripcion       P.Unit  Total", 10, 45);
    doc.text("=====================================", 10, 50);

    let y = 55;
    productos.forEach((p) => {
        let cantidadStr = p.cantidad.toString().padEnd(4); // Alinear cantidad
        let nombreStr = p.name.padEnd(15); // Alinear nombre
        let precioStr = p.price.toFixed(2).padStart(6); // Alinear precio unitario
        let totalStr = p.total.toFixed(2).padStart(6); // Alinear total de la fila

        doc.text(`${cantidadStr} ${nombreStr}    ${precioStr} ${totalStr}`, 10, y);
        y += 5;
    });
    doc.text("====================================", 10, y + 2);
    doc.text(`MONTO A PAGAR: ${totalFinal.toFixed(2)} Bs.`, 10, y + 8);
    doc.text(`EFECTIVO: ${efectivo.toFixed(2)} Bs.`, 10, y + 12);
    doc.text(`CAMBIO: ${cambio.toFixed(2)} Bs.`, 10, y + 16);


    // Convertir total a palabras
    let textoMonto = `SON ${numeroALetras(totalFinal)}`;
    let fontSize = 8; // Tamaño de fuente normal

    // Reducir tamaño si el texto es muy largo
    if (textoMonto.length > 40) {
        fontSize = 8; // Reducimos el tamaño
    }
    if (textoMonto.length > 60) {
        fontSize = 6; // Reducimos aún más si sigue siendo largo
    }

    doc.setFontSize(fontSize);
    doc.text(textoMonto, 10, y + 22);


    doc.text("====================================", 10, y + 28);
    doc.text(`PEDIDO: #${obtenerNumeroPedido()}`, 10, y + 33);

    let mesa = document.getElementById("numero-mesa").value.trim();
    mesa = mesa !== "" ? `MESA: ${mesa}` : "PARA LLEVAR";
    doc.text(mesa, 10, y + 38);

    doc.text(`EMITIDO POR: ARIEL CORI`, 10, y + 43);
    doc.text("Gracias por su compra", 10, y + 48);

    // Abrir PDF en nueva ventana
    doc.output("dataurlnewwindow");

    // Enviar a imprimir
    doc.autoPrint();
    window.open(doc.output("bloburl"), "_blank");

    // Reiniciar valores después de la venta
    productos = [];
    subtotal = 0;
    totalFinal = 0;
    document.getElementById("productosSeleccionados").innerHTML = "";
    document.getElementById("subtotal").textContent = "0.00";
    document.getElementById("total-final").textContent = "0.00";
    document.getElementById("cambio").textContent = "0.00";
    document.getElementById("efectivo-recibido").value = "";
    document.getElementById("descuento").value = "0";
}

function reiniciarVenta() {
    productos = [];
    subtotal = 0;
    totalFinal = 0;
    document.getElementById('productosSeleccionados').innerHTML = '';
    document.getElementById('subtotal').textContent = '0.00';
    document.getElementById('total-final').textContent = '0.00';
    document.getElementById('cambio').textContent = '0.00';
    document.getElementById('efectivo-recibido').value = '';
    document.getElementById('descuento').value = '0';
}
