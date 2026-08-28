<?php headerAdmin($data);
getModal('modalUsuarios', $data); ?>

<title>
  <?= $data['title']; ?>
</title>

<style>
  .vcl-layout{
    display:grid;
    grid-template-columns:1fr 380px;
    gap:20px;
    align-items:start;
  }
  @media (max-width:900px){ .vcl-layout{ grid-template-columns:1fr; } }

  .vcl-seleccion{
    background:var(--carbon-alto);
    border:1px solid var(--carbon-borde);
    border-radius:10px;
    padding:22px;
  }

  .vcl-tabs{ display:flex; gap:8px; margin-bottom:16px; }
  .vcl-tab{
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.9rem;
    padding:9px 18px;
    border-radius:6px;
    background:transparent;
    border:1px solid var(--carbon-borde);
    color:var(--hueso-tenue);
    cursor:pointer;
  }
  .vcl-tab.activo{
    background:var(--brasa);
    border-color:var(--brasa);
    color:var(--hueso);
  }

  .vcl-panel-productos{
    max-height:440px;
    overflow-y:auto;
    padding-right:4px;
  }

  /* Filas de producto generadas por functions_ventas.js — se mantienen
     las clases product-row / btn-product / quantity-input intactas. */
  .product-row{
    display:flex;
    align-items:center;
    gap:10px;
    background:var(--carbon);
    border:1px solid var(--carbon-borde);
    border-radius:6px;
    padding:9px 12px;
    margin-bottom:8px;
  }
  .product-row:hover{ border-color:var(--brasa-tenue); }
  button.btn-product{
    flex:1;
    text-align:left;
    background:transparent !important;
    border:none !important;
    color:var(--hueso) !important;
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.9rem;
    padding:4px 0 !important;
    cursor:pointer;
  }
  .quantity-input{
    width:60px;
    background:var(--carbon-alto);
    border:1px solid var(--carbon-borde);
    color:var(--hueso);
    border-radius:6px;
    padding:6px;
    text-align:center;
  }

  .vcl-datos-pedido{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
    margin-top:22px;
    padding-top:22px;
    border-top:1px solid var(--carbon-borde);
  }
  @media (max-width:560px){ .vcl-datos-pedido{ grid-template-columns:1fr; } }
  .vcl-datos-pedido h4{
    font-family:var(--font-body);
    font-size:0.8rem;
    text-transform:uppercase;
    letter-spacing:0.06em;
    color:var(--hueso-opaco);
    margin:0 0 10px;
  }
  .vcl-datos-pedido input{
    width:100%;
    background:var(--carbon);
    border:1px solid var(--carbon-borde);
    color:var(--hueso);
    border-radius:6px;
    padding:9px 12px;
    margin-bottom:8px;
    font-family:var(--font-body);
  }

  .vcl-ticket-panel{
    background:var(--carbon-alto);
    border:1px solid var(--carbon-borde);
    border-radius:10px;
    padding:22px;
    position:sticky;
    top:20px;
  }
  .vcl-ticket-panel h4{
    font-family:var(--font-display);
    font-weight:600;
    font-size:1.2rem;
    color:var(--hueso);
    margin:0 0 14px;
  }

  #productosSeleccionados{
    list-style:none;
    padding:0;
    margin:0 0 16px;
    max-height:180px;
    overflow-y:auto;
  }
  #productosSeleccionados .list-group-item{
    background:var(--carbon) !important;
    border:1px solid var(--carbon-borde) !important;
    color:var(--hueso) !important;
    border-radius:6px !important;
    margin-bottom:6px;
    font-family:var(--font-mono);
    font-size:0.85rem;
  }

  .vcl-fila-total{
    display:flex;
    justify-content:space-between;
    align-items:baseline;
    font-family:var(--font-mono);
    color:var(--hueso-tenue);
    padding:8px 0;
    border-top:1px dashed var(--carbon-borde);
  }
  .vcl-fila-total span:nth-child(2){ color:var(--hueso); font-weight:600; }

  .vcl-ticket-panel .campo-brasa{ margin-top:12px; }
  .vcl-ticket-panel button{ margin-top:8px; width:100%; }

  .vcl-total-final{
    display:flex;
    justify-content:space-between;
    align-items:baseline;
    margin-top:16px;
    padding-top:16px;
    border-top:1px dashed var(--carbon-borde);
  }
  .vcl-total-final span:first-child{ font-family:var(--font-body); color:var(--hueso-tenue); font-size:0.9rem; }
  .vcl-total-final span#total-final{ font-family:var(--font-mono); font-size:1.6rem; color:var(--brasa-viva); font-weight:600; }
</style>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="vcl-layout">

      <!-- Selección de productos -->
      <div class="vcl-seleccion">
        <div class="vcl-tabs">
          <button type="button" class="vcl-tab activo" onclick="cambiarTabVenta(this,'menu-container')">Pollos</button>
          <button type="button" class="vcl-tab" onclick="cambiarTabVenta(this,'bebida-container')">Bebidas</button>
          <button type="button" class="vcl-tab" onclick="cambiarTabVenta(this,'combo-container')">Combos</button>
        </div>

        <div class="scroll-container vcl-panel-productos" id="menu-container"></div>
        <div class="scroll-container vcl-panel-productos" id="bebida-container" style="display:none;"></div>
        <div class="scroll-container vcl-panel-productos" id="combo-container" style="display:none;"></div>

        <div class="vcl-datos-pedido">
          <div>
            <h4>Cliente</h4>
            <input type="text" id="nombre-cliente" placeholder="Nombre del cliente">
            <input type="text" id="numero-cliente" placeholder="Celular del cliente">
          </div>
          <div>
            <h4>Mesa</h4>
            <input type="text" id="numero-mesa" placeholder="Número de mesa">
            <button type="button" class="btn-secundario">Para llevar</button>
          </div>
        </div>
      </div>

      <!-- Ticket -->
      <div class="vcl-ticket-panel">
        <h4>Ticket</h4>

        <ul id="productosSeleccionados" class="list-group productos-list"></ul>

        <div class="vcl-fila-total">
          <span>Subtotal</span>
          <span><span id="subtotal">0.00</span> Bs.</span>
        </div>

        <div class="campo-brasa">
          <label for="efectivo-recibido">Efectivo recibido</label>
          <input type="number" class="form-control" id="efectivo-recibido" min="0" onchange="calcularCambio()">
        </div>
        <button type="button" class="btn-secundario" onclick="calcularCambio()">Calcular cambio</button>

        <div class="vcl-fila-total">
          <span>Cambio</span>
          <span><span id="cambio">0.00</span> <span id="moneda">Bs.</span></span>
        </div>

        <div class="campo-brasa">
          <label for="descuento">Descuento (%)</label>
          <input type="number" class="form-control" id="descuento" min="0" max="100" value="0" onchange="aplicarDescuento()">
        </div>
        <button type="button" class="btn-anular" onclick="aplicarDescuento()">Aplicar descuento</button>

        <div class="vcl-total-final">
          <span>Total final</span>
          <span id="total-final">0.00</span>
        </div>

        <button type="button" class="btn-primario" onclick="finalizarVenta()">Finalizar e imprimir</button>
        <button type="button" class="btn-secundario" onclick="finalizarVentaDoble()">Imprimir 2 tickets</button>
      </div>

    </div>

  </div>
  <!-- content-wrapper ends -->
  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Chicken Lindo · Sistema interno</span>
    </div>
  </footer>
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>

<script>
  function cambiarTabVenta(boton, containerId) {
    ['menu-container', 'bebida-container', 'combo-container'].forEach(function (id) {
      document.getElementById(id).style.display = (id === containerId) ? 'block' : 'none';
    });
    document.querySelectorAll('.vcl-tab').forEach(function (tab) {
      tab.classList.remove('activo');
    });
    boton.classList.add('activo');
  }
</script>

<?php footerAdmin($data); ?>