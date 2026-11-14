// Pantalla Vender: arma el pedido y lo envía completo al servidor en una sola petición.
// Los montos que se ven aquí son solo para mostrar: el servidor vuelve a calcular precios, total y cambio.
(function () {
  'use strict';

  const el = (id) => document.getElementById(id);
  const redondear = (n) => Math.round((n + Number.EPSILON) * 100) / 100;
  const dinero = (n) => redondear(n).toFixed(2);
  // Acepta "12,5" o "12.5"; lo que no sea un número cuenta como 0
  const numero = (texto) => {
    const n = parseFloat(String(texto).replace(',', '.'));
    return isFinite(n) && n > 0 ? n : 0;
  };

  let catalogo = [];            // productos activos que entrega el servidor
  const pedido = new Map();     // id_menu -> cantidad
  const estado = {
    categoria: 'todas',
    tipo: null,                 // 'mesa' o 'llevar'
    mesa: null,
    metodo: 'efectivo',         // 'efectivo', 'qr' o 'mixto'
    conDescuento: false,
    enviando: false
  };

  // ---------- Productos ----------
  function cargarCatalogo() {
    fetch(base_url + '/Ventas/getCatalogo')
      .then((respuesta) => respuesta.json())
      .then((datos) => {
        catalogo = datos.productos.map((p) => ({
          id: parseInt(p.id_menu, 10),
          nombre: p.nombre_menu,
          precio: parseFloat(p.precio_menu),
          categoriaId: String(p.categoria_id),
          categoria: p.nombre_categoria
        }));
        pintarCategorias();
        pintarProductos();
        pintarMesas(parseInt(datos.mesas, 10) || 0);
      })
      .catch(() => {
        el('vn-grilla').innerHTML = '<p class="vn-vacio">No se pudieron cargar los productos. Recarga la página (F5).</p>';
      });
  }

  function pintarCategorias() {
    const caja = el('vn-categorias');
    caja.innerHTML = '';
    const categorias = new Map();
    catalogo.forEach((p) => {
      const actual = categorias.get(p.categoriaId) || { nombre: p.categoria, total: 0 };
      actual.total++;
      categorias.set(p.categoriaId, actual);
    });
    const crear = (clave, nombre, total) => {
      const boton = document.createElement('button');
      boton.type = 'button';
      boton.className = 'vn-chip' + (estado.categoria === clave ? ' activo' : '');
      boton.dataset.categoria = clave;
      boton.textContent = nombre;
      const cuenta = document.createElement('small');
      cuenta.textContent = total;
      boton.appendChild(cuenta);
      caja.appendChild(boton);
    };
    crear('todas', 'Todos', catalogo.length);
    categorias.forEach((c, clave) => crear(clave, c.nombre, c.total));
  }

  function productosVisibles() {
    const busqueda = el('vn-buscar').value.trim().toLowerCase();
    return catalogo.filter((p) => {
      // Al buscar se mira en todas las categorías
      if (busqueda) {
        return p.nombre.toLowerCase().includes(busqueda);
      }
      return estado.categoria === 'todas' || p.categoriaId === estado.categoria;
    });
  }

  function pintarProductos() {
    const grilla = el('vn-grilla');
    grilla.innerHTML = '';
    const visibles = productosVisibles();
    if (!visibles.length) {
      const vacio = document.createElement('p');
      vacio.className = 'vn-vacio';
      vacio.textContent = 'No hay productos que coincidan.';
      grilla.appendChild(vacio);
      return;
    }
    visibles.forEach((p) => {
      const cantidad = pedido.get(p.id) || 0;
      const boton = document.createElement('button');
      boton.type = 'button';
      boton.className = 'vn-prod' + (cantidad ? ' en-pedido' : '');
      boton.dataset.id = p.id;
      boton.title = 'Agregar ' + p.nombre;

      const nombre = document.createElement('span');
      nombre.className = 'vn-prod-nombre';
      nombre.textContent = p.nombre;
      const precio = document.createElement('span');
      precio.className = 'vn-prod-precio';
      precio.textContent = dinero(p.precio) + ' Bs';
      boton.appendChild(nombre);
      boton.appendChild(precio);

      if (cantidad) {
        const marca = document.createElement('span');
        marca.className = 'vn-prod-cant';
        marca.textContent = cantidad;
        boton.appendChild(marca);
      }
      grilla.appendChild(boton);
    });
  }

  // ---------- Pedido ----------
  function cambiarCantidad(id, diferencia) {
    const nueva = (pedido.get(id) || 0) + diferencia;
    if (nueva <= 0) {
      pedido.delete(id);
    } else {
      pedido.set(id, Math.min(nueva, 200));
    }
    // Al empezar un pedido nuevo se quita el aviso de la venta anterior
    el('vn-resultado').classList.add('vn-oculto');
    ocultarAviso();
    pintarProductos();
    pintarPedido();
  }

  function pintarPedido() {
    const lista = el('vn-lineas');
    lista.innerHTML = '';
    if (!pedido.size) {
      const vacio = document.createElement('li');
      vacio.className = 'vn-vacio';
      vacio.textContent = 'Elige productos de la izquierda.';
      lista.appendChild(vacio);
    }
    pedido.forEach((cantidad, id) => {
      const p = catalogo.find((x) => x.id === id);
      if (!p) return;
      const fila = document.createElement('li');
      fila.className = 'vn-linea';
      fila.dataset.id = id;

      const nombre = document.createElement('span');
      nombre.className = 'vn-linea-nombre';
      nombre.textContent = p.nombre;
      nombre.title = p.nombre;

      const control = document.createElement('span');
      control.className = 'vn-cant';
      control.innerHTML = '<button type="button" class="vn-mini" data-accion="menos" aria-label="Quitar uno">−</button>'
        + '<span></span>'
        + '<button type="button" class="vn-mini" data-accion="mas" aria-label="Agregar uno">+</button>';
      control.querySelector('span').textContent = cantidad;

      const importe = document.createElement('span');
      importe.className = 'vn-linea-importe';
      importe.textContent = dinero(p.precio * cantidad);

      const quitar = document.createElement('button');
      quitar.type = 'button';
      quitar.className = 'vn-mini quitar';
      quitar.dataset.accion = 'quitar';
      quitar.setAttribute('aria-label', 'Quitar ' + p.nombre);
      quitar.textContent = '×';

      fila.appendChild(nombre);
      fila.appendChild(control);
      fila.appendChild(importe);
      fila.appendChild(quitar);
      lista.appendChild(fila);
    });
    pintarTotales();
  }

  // Calcula lo que se muestra; el servidor repite la cuenta al cobrar
  function calcular() {
    let subtotal = 0;
    pedido.forEach((cantidad, id) => {
      const p = catalogo.find((x) => x.id === id);
      if (p) subtotal += redondear(p.precio * cantidad);
    });
    subtotal = redondear(subtotal);
    const descuento = estado.conDescuento ? Math.min(numero(el('vn-descuento').value), 100) : 0;
    const total = redondear(subtotal * (1 - descuento / 100));

    let montoQr = 0;
    let enEfectivo = total;
    if (estado.metodo === 'qr') {
      montoQr = total;
      enEfectivo = 0;
    } else if (estado.metodo === 'mixto') {
      montoQr = redondear(Math.min(numero(el('vn-qr').value), total));
      enEfectivo = redondear(total - montoQr);
    }
    const recibido = estado.metodo === 'qr' ? 0 : redondear(numero(el('vn-recibido').value));
    return { subtotal, descuento, total, montoQr, enEfectivo, recibido, cambio: redondear(recibido - enEfectivo) };
  }

  function pintarTotales() {
    const c = calcular();
    el('vn-subtotal').textContent = dinero(c.subtotal);
    el('vn-descuento-monto').textContent = '-' + dinero(c.subtotal - c.total);
    el('vn-total').textContent = dinero(c.total);
    el('vn-en-efectivo').textContent = dinero(c.enEfectivo);

    const caja = el('vn-caja-cambio');
    const falta = c.cambio < 0 && el('vn-recibido').value.trim() !== '';
    caja.classList.toggle('falta', falta);
    el('vn-cambio-texto').textContent = falta ? 'Falta' : 'Cambio';
    el('vn-cambio').textContent = dinero(Math.abs(falta ? c.cambio : Math.max(c.cambio, 0)));
  }

  // ---------- Mesa o para llevar ----------
  function pintarMesas(cantidad) {
    const caja = el('vn-mesas');
    caja.innerHTML = '';
    const llevar = document.createElement('button');
    llevar.type = 'button';
    llevar.className = 'vn-mesa llevar';
    llevar.dataset.llevar = '1';
    llevar.textContent = 'Para llevar';
    caja.appendChild(llevar);
    for (let n = 1; n <= cantidad; n++) {
      const boton = document.createElement('button');
      boton.type = 'button';
      boton.className = 'vn-mesa';
      boton.dataset.mesa = n;
      boton.textContent = n;
      caja.appendChild(boton);
    }
  }

  function elegirTipo(tipo) {
    estado.tipo = tipo;
    if (tipo !== 'mesa') estado.mesa = null;
    document.querySelectorAll('.vn-mesa').forEach((b) => {
      const activo = b.dataset.llevar ? tipo === 'llevar' : (tipo === 'mesa' && parseInt(b.dataset.mesa, 10) === estado.mesa);
      b.classList.toggle('activo', activo);
    });
    el('vn-mesas').classList.remove('vn-falta');
    ocultarAviso();
  }

  // ---------- Forma de pago ----------
  function elegirMetodo(metodo) {
    estado.metodo = metodo;
    document.querySelectorAll('#vn-metodo .vn-opcion').forEach((b) => b.classList.toggle('activo', b.dataset.metodo === metodo));
    el('vn-bloque-qr').classList.toggle('vn-oculto', metodo !== 'mixto');
    el('vn-info-mixto').classList.toggle('vn-oculto', metodo !== 'mixto');
    el('vn-bloque-efectivo').classList.toggle('vn-oculto', metodo === 'qr');
    el('vn-bloque-efectivo').classList.toggle('mixto', metodo === 'mixto');
    el('vn-bloque-solo-qr').classList.toggle('vn-oculto', metodo !== 'qr');
    ocultarAviso();
    pintarTotales();
    if (metodo === 'efectivo') el('vn-recibido').focus();
    if (metodo === 'mixto') el('vn-qr').focus();
  }

  // ---------- Avisos ----------
  function mostrarAviso(texto) {
    el('vn-aviso').textContent = texto;
    el('vn-aviso').classList.remove('vn-oculto');
  }
  function ocultarAviso() {
    el('vn-aviso').classList.add('vn-oculto');
  }

  function mostrarResultado(datos, metodo) {
    const caja = el('vn-resultado');
    caja.innerHTML = '';
    const titulo = document.createElement('strong');
    titulo.textContent = 'Pedido #' + datos.numero_pedido + ' registrado';
    caja.appendChild(titulo);

    const detalle = document.createElement('div');
    if (metodo === 'qr') {
      detalle.textContent = 'Cobrado por QR: ' + dinero(datos.total) + ' Bs';
    } else {
      detalle.textContent = 'Cambio a entregar: ';
      const cambio = document.createElement('span');
      cambio.className = 'vn-resultado-cambio';
      cambio.textContent = dinero(datos.cambio) + ' Bs';
      detalle.appendChild(cambio);
    }
    caja.appendChild(detalle);

    if (!datos.impreso) {
      const alerta = document.createElement('div');
      alerta.className = 'vn-resultado-alerta';
      alerta.textContent = 'La venta quedó guardada, pero no se pudo imprimir. Revisa la impresora.';
      caja.appendChild(alerta);
    }
    caja.classList.remove('vn-oculto');
  }

  // ---------- Cobrar ----------
  function limpiarPedido() {
    pedido.clear();
    estado.tipo = null;
    estado.mesa = null;
    estado.conDescuento = false;
    el('vn-nota').value = '';
    el('vn-recibido').value = '';
    el('vn-qr').value = '';
    el('vn-descuento').value = '';
    el('vn-buscar').value = '';
    el('vn-fila-subtotal').classList.add('vn-oculto');
    el('vn-fila-descuento').classList.add('vn-oculto');
    el('vn-ver-descuento').textContent = 'Agregar descuento';
    elegirTipo(null);
    elegirMetodo('efectivo');
    pintarProductos();
    pintarPedido();
    el('vn-buscar').focus();
  }

  function cobrar(tickets) {
    if (estado.enviando) return;
    ocultarAviso();

    if (!pedido.size) {
      mostrarAviso('Agrega al menos un producto.');
      return;
    }
    if (!estado.tipo || (estado.tipo === 'mesa' && !estado.mesa)) {
      el('vn-mesas').classList.add('vn-falta');
      mostrarAviso('Elige el número de mesa o marca "Para llevar".');
      return;
    }
    const c = calcular();
    if (estado.metodo === 'mixto' && (c.montoQr <= 0 || c.montoQr >= c.total)) {
      mostrarAviso('En pago mixto, el monto por QR debe ser mayor que 0 y menor que el total.');
      el('vn-qr').focus();
      return;
    }
    if (estado.metodo !== 'qr' && c.recibido < c.enEfectivo) {
      mostrarAviso('El efectivo recibido no alcanza: faltan ' + dinero(c.enEfectivo - c.recibido) + ' Bs.');
      el('vn-recibido').focus();
      return;
    }

    const metodo = estado.metodo;
    const envio = {
      productos: Array.from(pedido, ([id, cantidad]) => ({ menu_id: id, cantidad: cantidad })),
      tipo_pedido: estado.tipo,
      mesa: estado.mesa,
      nota: el('vn-nota').value.trim(),
      metodo_pago: metodo,
      efectivo_recibido: c.recibido,
      monto_qr: c.montoQr,
      descuento: c.descuento,
      tickets: tickets
    };

    // Se bloquean los botones para que un doble clic no registre la venta dos veces
    estado.enviando = true;
    el('vn-cobrar-1').disabled = true;
    el('vn-cobrar-2').disabled = true;

    fetch(base_url + '/Ventas/registrarVentaCompleta', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(envio)
    })
      .then((respuesta) => respuesta.json())
      .then((datos) => {
        if (datos.status) {
          limpiarPedido();
          mostrarResultado(datos, metodo);
        } else if (datos.requiere_caja) {
          window.location = base_url + '/caja?aviso=caja_cerrada';
        } else {
          mostrarAviso(datos.msg || 'No se pudo registrar la venta.');
        }
      })
      .catch(() => {
        mostrarAviso('No hubo respuesta del sistema. Antes de volver a cobrar, revisa en Tickets si la venta se registró.');
      })
      .finally(() => {
        estado.enviando = false;
        el('vn-cobrar-1').disabled = false;
        el('vn-cobrar-2').disabled = false;
      });
  }

  function vaciar() {
    if (!pedido.size) {
      limpiarPedido();
      return;
    }
    Swal.fire({
      title: '¿Vaciar el pedido?',
      text: 'Se quitan todos los productos elegidos.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, vaciar',
      cancelButtonText: 'Seguir con el pedido'
    }).then((resultado) => {
      if (resultado.isConfirmed) limpiarPedido();
    });
  }

  // ---------- Eventos ----------
  document.addEventListener('DOMContentLoaded', function () {
    cargarCatalogo();

    el('vn-buscar').addEventListener('input', pintarProductos);
    // Enter en el buscador agrega el primer producto encontrado
    el('vn-buscar').addEventListener('keydown', function (evento) {
      if (evento.key !== 'Enter') return;
      const visibles = productosVisibles();
      if (this.value.trim() && visibles.length) {
        cambiarCantidad(visibles[0].id, 1);
        this.select();
      }
    });

    el('vn-categorias').addEventListener('click', function (evento) {
      const boton = evento.target.closest('.vn-chip');
      if (!boton) return;
      estado.categoria = boton.dataset.categoria;
      el('vn-buscar').value = '';
      pintarCategorias();
      pintarProductos();
    });

    el('vn-grilla').addEventListener('click', function (evento) {
      const boton = evento.target.closest('.vn-prod');
      if (boton) cambiarCantidad(parseInt(boton.dataset.id, 10), 1);
    });

    el('vn-lineas').addEventListener('click', function (evento) {
      const boton = evento.target.closest('[data-accion]');
      if (!boton) return;
      const id = parseInt(boton.closest('.vn-linea').dataset.id, 10);
      if (boton.dataset.accion === 'mas') cambiarCantidad(id, 1);
      if (boton.dataset.accion === 'menos') cambiarCantidad(id, -1);
      if (boton.dataset.accion === 'quitar') cambiarCantidad(id, -(pedido.get(id) || 0));
    });

    el('vn-mesas').addEventListener('click', function (evento) {
      const boton = evento.target.closest('.vn-mesa');
      if (!boton) return;
      if (boton.dataset.llevar) {
        elegirTipo('llevar');
      } else {
        estado.mesa = parseInt(boton.dataset.mesa, 10);
        elegirTipo('mesa');
      }
    });

    el('vn-metodo').addEventListener('click', function (evento) {
      const boton = evento.target.closest('.vn-opcion');
      if (boton) elegirMetodo(boton.dataset.metodo);
    });

    ['vn-recibido', 'vn-qr', 'vn-descuento'].forEach(function (id) {
      el(id).addEventListener('input', function () {
        ocultarAviso();
        pintarTotales();
      });
    });

    el('vn-ver-descuento').addEventListener('click', function () {
      estado.conDescuento = !estado.conDescuento;
      el('vn-fila-subtotal').classList.toggle('vn-oculto', !estado.conDescuento);
      el('vn-fila-descuento').classList.toggle('vn-oculto', !estado.conDescuento);
      this.textContent = estado.conDescuento ? 'Quitar descuento' : 'Agregar descuento';
      if (estado.conDescuento) {
        el('vn-descuento').focus();
      } else {
        el('vn-descuento').value = '';
      }
      pintarTotales();
    });

    el('vn-vaciar').addEventListener('click', vaciar);
    el('vn-cobrar-1').addEventListener('click', function () { cobrar(1); });
    el('vn-cobrar-2').addEventListener('click', function () { cobrar(2); });

    // Atajos de teclado: F2 buscar, F8 y F9 cobrar, Esc vaciar
    document.addEventListener('keydown', function (evento) {
      if (document.querySelector('.swal2-container')) return; // hay una ventana de confirmación abierta
      if (evento.key === 'F2') {
        evento.preventDefault();
        el('vn-buscar').focus();
        el('vn-buscar').select();
      } else if (evento.key === 'F8') {
        evento.preventDefault();
        cobrar(1);
      } else if (evento.key === 'F9') {
        evento.preventDefault();
        cobrar(2);
      } else if (evento.key === 'Escape') {
        evento.preventDefault();
        if (el('vn-buscar').value) {
          el('vn-buscar').value = '';
          pintarProductos();
        } else {
          vaciar();
        }
      }
    });

    el('vn-buscar').focus();
  });
})();
