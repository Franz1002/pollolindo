function cargarProductos(url, containerId) {
  fetch(url)
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById(containerId);

      data.forEach(producto => {
        // Crear el HTML para cada producto
        const productRow = document.createElement('div');
        productRow.classList.add('product-row');

        const button = document.createElement('button');
        button.classList.add('btn', 'btn-primary', 'btn-product');
        button.textContent = `${producto.nombre_menu} - ${producto.precio_menu} Bs.`;
        button.setAttribute('data-price', producto.precio_menu);
        button.setAttribute('data-id', producto.id_menu);

        // Cambiar el color del texto a negro
        button.style.color = 'black';  // Aplica el color negro al texto del botón

        const quantityInput = document.createElement('input');
        quantityInput.type = 'number';
        quantityInput.classList.add('form-control', 'quantity-input');
        quantityInput.value = 1;
        quantityInput.min = 1;

        // Añadir el botón y el input al contenedor de producto
        productRow.appendChild(button);
        productRow.appendChild(quantityInput);

        // Insertar el producto en el contenedor
        container.appendChild(productRow);
      });
    })
    .catch(error => {
      console.error('Error al cargar los productos:', error);
    });
}

// Llamar a la función con las URLs y los contenedores correspondientes
cargarProductos(base_url + '/Ventas/getMenu/', 'menu-container');
cargarProductos(base_url + '/Ventas/getBebida/', 'bebida-container');
cargarProductos(base_url + '/Ventas/getCombo/', 'combo-container');

let productos = [];
let subtotal = 0,
  totalFinal = 0;

//eventos para agregar productos
function agregarProducto(name, price, quantity, menu_id) {
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
      total: price * quantity,
      menu_id: menu_id  // Asegúrate de que el menu_id se esté pasando correctamente
    });
  }

  // Actualizar el ticket después de agregar el producto
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
    li.innerHTML = `${p.cantidad} x ${p.name} - ${p.total.toFixed(2)} Bs. 
    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})"> X</button>`;
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

  if (totalFinal <= 0) {
    document.getElementById('cambio').textContent = "N/A";
    return;
  }

  let cambio = efectivo - totalFinal;
  document.getElementById('cambio').textContent = cambio < 0 ? "Falta dinero" : cambio.toFixed(2);
}

function convertirEntero(num) {
  const unidades = ["", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE"];
  const decenas = ["", "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE"];
  const decenasAltas = ["", "", "VEINTE", "VEINTIUNO", "VEINTIDOS", "VEINTITRES", "VEINTICUATRO", "VEINTICINCO", "VEINTISEIS", "VEINTISIETE"];
  const centenas = ["", "CIENTO", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS"];
  const miles = ["", "MIL"];

  if (num === 0) return "CERO";

  let letras = '';

  // Desglosamos los números
  let c = Math.floor(num / 100); // Centenas
  let d = Math.floor((num % 100) / 10); // Decenas
  let u = num % 10; // Unidades

  if (c > 0) {
    letras += centenas[c] + " ";
  }

  if (d === 1 && u > 0) { // Para números entre 10 y 19
    letras += decenas[u] + " ";
  } else {
    if (d >= 2) {
      letras += decenasAltas[d] + " ";
    }
    if (u > 0) {
      letras += unidades[u] + " ";
    }
  }

  return letras.trim();
}

function numeroALetras(num) {
  let [entero, decimal] = num.toFixed(2).split(".");

  // Convertir la parte entera
  let resultado = convertirEntero(parseInt(entero));

  // Si el valor decimal es mayor que 0, lo convertimos
  if (parseInt(decimal) > 0) {
    resultado += ` CON ${convertirEntero(parseInt(decimal))} CENTAVOS`;
  } else {
    resultado += " BOLIVIANOS";
  }

  return resultado;
}







async function registrarCliente(nombre) {
  const cliente = { nombre: nombre};

  try {
    const response = await fetch('http://localhost/pollo/Clientes/setCliente', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(cliente)
    });

    const textResponse = await response.text();  // Obtiene la respuesta en texto

    // Intentamos convertirla en JSON
    try {
      const data = JSON.parse(textResponse);
      console.log("Respuesta parseada:", data);
      if (data.status) {
        return data.id_cliente;  // Retornamos el ID del cliente si todo está bien
      } else {
        alert('Error al registrar el cliente: ' + (data.msg || 'Desconocido'));
        return null;
      }
    } catch (jsonError) {
      console.error("Error al convertir la respuesta a JSON:", jsonError);
      alert("El servidor devolvió un formato inválido.");
      return null;
    }

  } catch (error) {
    console.error('Error al registrar el cliente:', error);
    alert('Hubo un error al registrar el cliente. Inténtalo de nuevo.');
    return null;
  }
}

// Función para finalizar la venta
async function finalizarVenta() {
  let efectivo = parseFloat(document.querySelector("#efectivo-recibido").value) || 0;
  let totalFinal = parseFloat(document.querySelector("#total-final").textContent) || 0;

  if (productos.length === 0) {
    alert("No hay productos en la venta.");
    return;
  }

 

  if (totalFinal <= 0) {
    alert("El total final no puede ser 0.");
    return;
  }

  let cambio = efectivo - totalFinal;
  if (cambio < 0) {
    alert("El efectivo recibido es insuficiente.");
    return;
  }

  // Obtener datos del cliente
  let nombreCliente = document.getElementById("nombre-cliente").value.trim();
  //let celularCliente = document.getElementById("numero-cliente").value.trim();

  // Si los campos están vacíos, asignar null para que no se registre un cliente
  let cliente_id = null;
  if (nombreCliente !== "") {
    cliente_id = await registrarCliente(nombreCliente);
  }

  console.log("ID del cliente obtenido:", cliente_id);  // <-- DEPURACIÓN


  // Recoger los detalles de la venta
  let productosSeleccionados = productos.map(producto => ({
    //menu_id: producto.menu_id,  // Asegúrate de que 'menu_id' esté presente
    menu_id: parseInt(producto.menu_id, 10),
    cantidad: producto.cantidad,
    subtotal: producto.total  // Este es el subtotal calculado previamente
  }));

  // Obtener el descuento
  let descuento = parseFloat(document.getElementById('descuento').value) || 0;

  // Registrar la venta
  console.log("Llamando a registrarVenta con cliente_id:", cliente_id);
  await registrarVenta(cliente_id, efectivo, cambio, totalFinal, descuento, productosSeleccionados);
  console.log("Venta registrada correctamente.");  // <-- Si este mensaje no aparece, la función no se ejecutó

}

// Función para finalizar la venta
async function finalizarVentaDoble() {
  let efectivo = parseFloat(document.querySelector("#efectivo-recibido").value) || 0;
  let totalFinal = parseFloat(document.querySelector("#total-final").textContent) || 0;

  if (productos.length === 0) {
    alert("No hay productos en la venta.");
    return;
  }

  if (totalFinal <= 0) {
    alert("El total final no puede ser 0.");
    return;
  }

  let cambio = efectivo - totalFinal;
  if (cambio < 0) {
    alert("El efectivo recibido es insuficiente.");
    return;
  }

  // Obtener datos del cliente
  let nombreCliente = document.getElementById("nombre-cliente").value.trim();
 // let celularCliente = document.getElementById("numero-cliente").value.trim();

  // Si los campos están vacíos, asignar null para que no se registre un cliente
  let cliente_id = null;
  if (nombreCliente !== "") {
    cliente_id = await registrarCliente(nombreCliente);
  }

  console.log("ID del cliente obtenido:", cliente_id);  // <-- DEPURACIÓN


  // Recoger los detalles de la venta
  let productosSeleccionados = productos.map(producto => ({
    //menu_id: producto.menu_id,  // Asegúrate de que 'menu_id' esté presente
    menu_id: parseInt(producto.menu_id, 10),
    cantidad: producto.cantidad,
    subtotal: producto.total  // Este es el subtotal calculado previamente
  }));

  // Obtener el descuento
  let descuento = parseFloat(document.getElementById('descuento').value) || 0;

  // Registrar la venta
  console.log("Llamando a registrarVentaDoble con cliente_id:", cliente_id);
  await registrarVentaDoble(cliente_id, efectivo, cambio, totalFinal, descuento, productosSeleccionados);
  console.log("Venta registrada correctamente.");  // <-- Si este mensaje no aparece, la función no se ejecutó

}

async function obtenerUsuarioId() {
  try {
    const response = await fetch("http://localhost/pollo/Usuarios/getUsuarioId");
    const data = await response.json();
    if (data.usuario_id && data.usuario_id.id) {
      return data.usuario_id.id; // Aquí obtenemos el ID directamente
    } else {
      console.error("No se pudo obtener el usuario_id");
      return null;
    }
  } catch (error) {
    console.error("Error al obtener el usuario_id:", error);
    return null;
  }
}

async function obtenerUsuarioNombre() {
  try {
    const response = await fetch('http://localhost/pollo/Usuarios/getUsuarioId');
    const data = await response.json();

    // Retorna un objeto con el nombre y apellido
    return {
      nombre: data.usuario_id?.nombre ?? null,
      apellido: data.usuario_id?.apellido ?? null
    };
  } catch (error) {
    console.error('Error al obtener el nombre y apellido del usuario:', error);
    return { nombre: null, apellido: null }; // En caso de error, retornamos null para ambos campos
  }
}

// Función para registrar la venta y obtener el número de pedido
async function registrarVenta(cliente_id, efectivo, cambio, totalFinal, descuento, productosSeleccionados) {
  const usuario_id = await obtenerUsuarioId();
  console.log(usuario_id); // Aquí puedes ver el id del usuario en la consola

  if (usuario_id === null) {
    console.log("No se pudo obtener el usuario_id");
    return;
  }
  const ventaData = {
    usuario_id: usuario_id,
    fecha_venta: new Date().toISOString(),
    total_venta: totalFinal,
  };

  try {
    const responseVenta = await fetch('http://localhost/pollo/Ventas/registrarVenta', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(ventaData)
    });

    const responseVentaText = await responseVenta.text();
    console.log("Respuesta del servidor:", responseVentaText);

    let dataVenta;
    try {
      dataVenta = JSON.parse(responseVentaText);
    } catch (error) {
      console.error("Error al analizar la respuesta JSON:", error);
      return;
    }

    if (dataVenta.status) {
      const venta_id = parseInt(dataVenta.id_venta, 10);
      const detallesData = productosSeleccionados.map(producto => ({
        venta_id: venta_id,
        menu_id: producto.menu_id,
        cantidad: producto.cantidad,
        subtotal: producto.subtotal
      }));

      const responseDetalles = await fetch('http://localhost/pollo/Ventas/registrarDetalleVenta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ venta_id: venta_id, productos: detallesData })
      });

      const responseDetallesText = await responseDetalles.text();
      console.log("Respuesta del servidor detalles:", responseDetallesText);

      let dataDetalles;
      try {
        dataDetalles = JSON.parse(responseDetallesText);
      } catch (error) {
        console.error("Error al analizar la respuesta JSON de detalles:", error);
        return;
      }

      if (dataDetalles.status) {
        const ticketData = {
          venta_id: venta_id,
          cliente_id: cliente_id ?? 0,
          total: totalFinal,
          fecha_emision: new Date().toISOString(),
          emitido_por: usuario_id,
          descuento: descuento,
          efectivo_recibido: efectivo,
          cambio: cambio,
          productos: productosSeleccionados.map(producto => ({
            nombre_menu: producto.nombre,
            cantidad: producto.cantidad,
            precio_menu: producto.precio,
            subtotal: producto.subtotal
          }))
        };

        const responseTicket = await fetch('http://localhost/pollo/Ventas/registrarTicket', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(ticketData)
        });

        const responseTicketText = await responseTicket.text();
        console.log("Respuesta del servidor ticket:", responseTicketText);

        let dataTicket;
        try {
          dataTicket = JSON.parse(responseTicketText);
        } catch (error) {
          console.error("Error al analizar la respuesta JSON del ticket:", error);
          return;
        }

        if (!dataTicket.status) {
          alert("Error al generar el ticket: " + dataTicket.msg);
        }
      } else {
        alert("Error al registrar los detalles de la venta: " + dataDetalles.msg);
      }
    } else {
      alert("Error al registrar la venta: " + dataVenta.msg);
    }
  } catch (error) {
    console.error("Error al registrar la venta:", error);
    alert("Hubo un problema al registrar la venta.");
  }
}



// Función para registrar la venta y obtener el número de pedido
async function registrarVentaDoble(cliente_id, efectivo, cambio, totalFinal, descuento, productosSeleccionados) {
  const usuario_id = await obtenerUsuarioId();
  console.log(usuario_id); // Aquí puedes ver el id del usuario en la consola

  if (usuario_id === null) {
    console.log("No se pudo obtener el usuario_id");
    return;
  }
  const ventaData = {
    usuario_id: usuario_id,
    fecha_venta: new Date().toISOString(),
    total_venta: totalFinal,
  };

  try {
    const responseVenta = await fetch('http://localhost/pollo/Ventas/registrarVenta', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(ventaData)
    });

    const responseVentaText = await responseVenta.text();
    console.log("Respuesta del servidor:", responseVentaText);

    let dataVenta;
    try {
      dataVenta = JSON.parse(responseVentaText);
    } catch (error) {
      console.error("Error al analizar la respuesta JSON:", error);
      return;
    }

    if (dataVenta.status) {
      const venta_id = parseInt(dataVenta.id_venta, 10);
      const detallesData = productosSeleccionados.map(producto => ({
        venta_id: venta_id,
        menu_id: producto.menu_id,
        cantidad: producto.cantidad,
        subtotal: producto.subtotal
      }));

      const responseDetalles = await fetch('http://localhost/pollo/Ventas/registrarDetalleVenta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ venta_id: venta_id, productos: detallesData })
      });

      const responseDetallesText = await responseDetalles.text();
      console.log("Respuesta del servidor detalles:", responseDetallesText);

      let dataDetalles;
      try {
        dataDetalles = JSON.parse(responseDetallesText);
      } catch (error) {
        console.error("Error al analizar la respuesta JSON de detalles:", error);
        return;
      }

      if (dataDetalles.status) {
        const ticketData = {
          venta_id: venta_id,
          cliente_id: cliente_id ?? 0,
          total: totalFinal,
          fecha_emision: new Date().toISOString(),
          emitido_por: usuario_id,
          descuento: descuento,
          efectivo_recibido: efectivo,
          cambio: cambio,
          productos: productosSeleccionados.map(producto => ({
            nombre_menu: producto.nombre,
            cantidad: producto.cantidad,
            precio_menu: producto.precio,
            subtotal: producto.subtotal
          }))
        };

        const responseTicket = await fetch('http://localhost/pollo/Ventas/registrarTicketDoble', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(ticketData)
        });

        const responseTicketText = await responseTicket.text();
        console.log("Respuesta del servidor ticket:", responseTicketText);

        let dataTicket;
        try {
          dataTicket = JSON.parse(responseTicketText);
        } catch (error) {
          console.error("Error al analizar la respuesta JSON del ticket:", error);
          return;
        }

        if (!dataTicket.status) {
          alert("Error al generar el ticket: " + dataTicket.msg);
        }
      } else {
        alert("Error al registrar los detalles de la venta: " + dataDetalles.msg);
      }
    } else {
      alert("Error al registrar la venta: " + dataVenta.msg);
    }
  } catch (error) {
    console.error("Error al registrar la venta:", error);
    alert("Hubo un problema al registrar la venta.");
  }
}








function agregarEventoProductos(containerId) {
  document.getElementById(containerId).addEventListener('click', function (event) {
    if (event.target && event.target.matches('button.btn-product')) {
      const price = parseFloat(event.target.dataset.price);
      const quantityInput = event.target.nextElementSibling;
      const quantity = parseInt(quantityInput.value);
      const name = event.target.textContent.split("-")[0].trim();
      const menu_id = event.target.dataset.id;  // Aquí se accede correctamente a data-id

      agregarProducto(name, price, quantity, menu_id);  // Se pasa el menu_id correctamente
    }
  });
}

["menu-container", "bebida-container", "combo-container"].forEach(agregarEventoProductos);

// Limpiar los campos cuando la página se recargue
window.addEventListener("DOMContentLoaded", (event) => {
  // Limpiar los campos
  document.getElementById("nombre-cliente").value = "";
  document.getElementById("numero-cliente").value = "";
  document.getElementById("numero-mesa").value = "";
  document.getElementById('efectivo-recibido').value = '';
  document.getElementById('descuento').value = '0';
});

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
  document.getElementById('nombre-cliente').value = '';
  document.getElementById('numero-cliente').value = '';
  document.getElementById('numero-mesa').value = '';
}




// Modificar la función generarTicketPDF para aceptar el numero_pedido y obtener el nombre del usuario
/*async function generarTicketPDF(efectivo, cambio, numero_pedido) {
  efectivo = parseFloat(efectivo) || 0;
  cambio = parseFloat(cambio) || 0; // Convierte a número y si es NaN, asigna 0

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  // Información de la empresa y cliente
  doc.setFont("courier", "bold");
  doc.setFontSize(14);
  doc.text("Chicken Lindo Srl.", 15, 10);
  doc.text("Pollo Lindo.", 25, 15);
  doc.setFontSize(8);
  doc.text("Cochabamba - Bolivia", 24, 20);
  doc.text("NIT: 1009311029", 10, 25);
  doc.text(`FECHA: ${new Date().toLocaleDateString()}`, 10, 30);

  let cliente = document.getElementById("nombre-cliente").value.trim();
  cliente = cliente !== "" ? cliente : "SIN NOMBRE";
  doc.text(`CLIENTE: ${cliente}`, 10, 35);

  doc.text("=====================================", 10, 40);
  doc.text("Cant  Descripcion       P.Unit  Total", 10, 45);
  doc.text("=====================================", 10, 50);

  let y = 55;
  productos.forEach((p) => {
    let cantidadStr = p.cantidad.toString().padEnd(4);
    let nombreStr = p.name.padEnd(15);
    let precioStr = p.price.toFixed(2).padStart(6);
    let totalStr = p.total.toFixed(2).padStart(6);

    doc.text(`${cantidadStr} ${nombreStr}    ${precioStr} ${totalStr}`, 10, y);
    y += 5;
  });

  doc.text("====================================", 10, y + 2);
  doc.text("MONTO A PAGAR: " + totalFinal.toFixed(2) + " Bs.", 10, y + 8);
  doc.text("EFECTIVO: " + efectivo.toFixed(2) + " Bs.", 10, y + 12);
  doc.text("CAMBIO: " + cambio.toFixed(2) + " Bs.", 10, y + 16);

  let textoMonto = `SON ${numeroALetras(totalFinal)}`;
  let fontSize = 8;

  if (textoMonto.length > 40) fontSize = 6; // Reducir tamaño si el texto es largo
  doc.setFontSize(fontSize);
  doc.text(textoMonto, 10, y + 22);

  doc.text("====================================", 10, y + 28);
  doc.text(`PEDIDO: #${numero_pedido}`, 10, y + 33); // Aquí usamos el numero_pedido

  let mesa = document.getElementById("numero-mesa").value.trim();
  mesa = mesa !== "" ? `MESA: ${mesa}` : "PARA LLEVAR";
  doc.text(mesa, 10, y + 38);

  // Obtener el nombre y apellido del usuario que emitió el ticket
  const usuario = await obtenerUsuarioNombre(); // Llamamos a la función asincrónica
  const nombreEmitidoPor = usuario.nombre && usuario.apellido ? `${usuario.nombre} ${usuario.apellido}` : "Desconocido";

  doc.text(`EMITIDO POR: ${nombreEmitidoPor}`, 10, y + 43); // Usamos el nombre y apellido del usuario

  doc.text("Gracias por su compra", 10, y + 48);

  doc.output("dataurlnewwindow");
  doc.autoPrint();
  window.open(doc.output("bloburl"), "_blank");
}*/
