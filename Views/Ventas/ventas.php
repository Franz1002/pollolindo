<?php headerAdmin($data); ?>

<style>
  /* Pantalla Vender: productos a la izquierda, pedido y cobro siempre visibles a la derecha */
  .vn-contenido{ padding:16px 20px !important; }
  .vn-layout{
    display:grid;
    grid-template-columns:minmax(0,1fr) 410px;
    gap:16px;
    height:calc(100vh - 104px);
    min-height:540px;
  }
  .vn-oculto{ display:none !important; }

  /* ----- Productos ----- */
  .vn-productos, .vn-pedido{
    background:var(--carbon-alto);
    border:1px solid var(--carbon-borde);
    border-radius:var(--radius-lg);
    display:flex;
    flex-direction:column;
    min-height:0;
  }
  .vn-productos{ padding:16px; }
  .vn-campo{
    width:100%;
    background:var(--carbon);
    border:1px solid var(--carbon-borde);
    color:var(--hueso);
    border-radius:var(--radius);
    padding:10px 12px;
    font-family:var(--font-body);
    font-size:0.95rem;
  }
  .vn-campo:focus{ border-color:var(--brasa-viva); outline:none; }
  .vn-campo::placeholder{ color:var(--hueso-opaco); }

  .vn-categorias{ display:flex; flex-wrap:wrap; gap:8px; margin:12px 0; }
  .vn-chip{
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.9rem;
    padding:9px 16px;
    border-radius:var(--radius);
    background:transparent;
    border:1px solid var(--carbon-borde);
    color:var(--hueso-tenue);
    cursor:pointer;
  }
  .vn-chip:hover{ border-color:var(--hueso-opaco); color:var(--hueso); }
  .vn-chip.activo{ background:var(--brasa); border-color:var(--brasa); color:var(--hueso); }
  .vn-chip small{ font-family:var(--font-mono); font-weight:500; opacity:0.75; margin-left:4px; }

  .vn-grilla{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(138px,1fr));
    gap:10px;
    overflow-y:auto;
    padding:2px 4px 2px 2px;
    align-content:start;
    flex:1;
    min-height:0;
  }
  .vn-prod{
    position:relative;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    min-height:78px;
    text-align:left;
    padding:10px 12px;
    background:var(--carbon);
    border:1px solid var(--carbon-borde);
    border-radius:8px;
    cursor:pointer;
    transition:border-color 0.12s ease, transform 0.06s ease;
  }
  .vn-prod:hover{ border-color:var(--brasa); }
  .vn-prod:active{ transform:scale(0.97); }
  .vn-prod.en-pedido{ border-color:var(--brasa-viva); background:rgba(199,81,31,0.10); }
  .vn-prod-nombre{
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.95rem;
    line-height:1.2;
    color:var(--hueso);
    padding-right:26px;
  }
  .vn-prod-precio{ font-family:var(--font-mono); font-size:0.9rem; color:var(--brasa-viva); }
  .vn-prod-cant{
    position:absolute;
    top:8px;
    right:8px;
    min-width:24px;
    height:24px;
    padding:0 6px;
    border-radius:12px;
    background:var(--brasa);
    color:var(--hueso);
    font-family:var(--font-mono);
    font-weight:600;
    font-size:0.82rem;
    line-height:24px;
    text-align:center;
  }
  .vn-vacio{ color:var(--hueso-opaco); font-size:0.9rem; padding:18px 4px; }

  /* ----- Pedido ----- */
  .vn-pedido{ padding:14px 16px; gap:9px; overflow-y:auto; }
  .vn-titulo{ display:flex; justify-content:space-between; align-items:center; }
  .vn-titulo h2{
    font-family:var(--font-display);
    font-weight:600;
    font-size:1.2rem;
    color:var(--hueso);
    margin:0;
  }
  .vn-enlace{
    background:none;
    border:none;
    padding:2px 0;
    color:var(--hueso-tenue);
    font-family:var(--font-body);
    font-size:0.82rem;
    cursor:pointer;
    text-decoration:underline;
  }
  .vn-enlace:hover{ color:var(--hueso); }

  .vn-segmento{ display:grid; gap:6px; }
  .vn-segmento.dos{ grid-template-columns:1fr 1fr; }
  .vn-segmento.tres{ grid-template-columns:1fr 1fr 1fr; }
  .vn-opcion{
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.9rem;
    padding:9px 6px;
    border-radius:var(--radius);
    background:transparent;
    border:1px solid var(--carbon-borde);
    color:var(--hueso-tenue);
    cursor:pointer;
  }
  .vn-opcion:hover{ border-color:var(--hueso-opaco); color:var(--hueso); }
  .vn-opcion.activo{ background:var(--carbon-mas-alto); border-color:var(--brasa-viva); color:var(--hueso); }

  .vn-mesas{ display:grid; grid-template-columns:repeat(9,1fr); gap:5px; }
  .vn-mesa.llevar{ grid-column:span 3; font-family:var(--font-body); }
  .vn-mesa{
    font-family:var(--font-mono);
    font-weight:600;
    font-size:0.88rem;
    padding:6px 0;
    border-radius:var(--radius);
    background:var(--carbon);
    border:1px solid var(--carbon-borde);
    color:var(--hueso-tenue);
    cursor:pointer;
  }
  .vn-mesa:hover{ border-color:var(--hueso-opaco); color:var(--hueso); }
  .vn-mesa.activo{ background:var(--brasa); border-color:var(--brasa); color:var(--hueso); }
  .vn-falta{ box-shadow:0 0 0 2px var(--oxido); border-radius:var(--radius); }

  .vn-lineas{
    list-style:none;
    margin:0;
    padding:0;
    flex:1;
    min-height:70px;
    overflow-y:auto;
    border-top:1px solid var(--carbon-borde);
    border-bottom:1px solid var(--carbon-borde);
  }
  .vn-linea{
    display:grid;
    grid-template-columns:minmax(0,1fr) auto 64px 26px;
    align-items:center;
    gap:8px;
    padding:6px 2px;
    border-bottom:1px dashed var(--carbon-borde);
  }
  .vn-linea:last-child{ border-bottom:none; }
  .vn-linea-nombre{
    font-family:var(--font-body);
    font-size:0.9rem;
    color:var(--hueso);
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .vn-cant{ display:flex; align-items:center; gap:6px; }
  .vn-cant span{ font-family:var(--font-mono); font-weight:600; color:var(--hueso); min-width:20px; text-align:center; }
  .vn-mini{
    width:26px;
    height:26px;
    border-radius:var(--radius);
    background:var(--carbon);
    border:1px solid var(--carbon-borde);
    color:var(--hueso);
    font-size:1rem;
    line-height:1;
    cursor:pointer;
    padding:0;
  }
  .vn-mini:hover{ border-color:var(--brasa-viva); }
  .vn-mini.quitar{ border-color:transparent; background:transparent; color:var(--hueso-opaco); }
  .vn-mini.quitar:hover{ color:var(--oxido-claro); }
  .vn-linea-importe{ font-family:var(--font-mono); font-size:0.88rem; color:var(--hueso); text-align:right; }

  .vn-fila{
    display:flex;
    justify-content:space-between;
    align-items:baseline;
    font-family:var(--font-body);
    font-size:0.88rem;
    color:var(--hueso-tenue);
  }
  .vn-fila b{ font-family:var(--font-mono); font-weight:500; color:var(--hueso); }
  .vn-total b{ font-size:1.7rem; font-weight:600; color:var(--brasa-viva); }
  .vn-total span{ font-size:0.95rem; color:var(--hueso); }

  .vn-par{ display:grid; grid-template-columns:1fr 1fr; gap:8px; align-items:end; }
  .vn-par.mixto{ grid-template-columns:1fr 1fr 1fr; }
  .vn-par label{ display:block; font-size:0.78rem; color:var(--hueso-tenue); margin:0 0 4px; }
  .vn-par .vn-campo{ font-family:var(--font-mono); font-size:1.05rem; padding:8px 10px; }
  .vn-cambio{
    border-radius:var(--radius);
    padding:8px 10px;
    background:rgba(110,124,87,0.2);
    border:1px solid var(--oliva);
    color:var(--oliva-claro);
    font-family:var(--font-body);
    font-size:0.78rem;
  }
  .vn-cambio b{ display:block; font-family:var(--font-mono); font-size:1.15rem; font-weight:600; }
  .vn-cambio.falta{ background:rgba(162,59,46,0.18); border-color:var(--oxido); color:var(--oxido-claro); }
  .vn-info{ font-size:0.85rem; color:var(--hueso-tenue); }

  .vn-aviso{
    border-radius:var(--radius);
    padding:8px 10px;
    font-size:0.85rem;
    background:rgba(162,59,46,0.18);
    border:1px solid var(--oxido);
    color:var(--oxido-claro);
  }
  .vn-resultado{
    border-radius:var(--radius);
    padding:10px 12px;
    background:rgba(110,124,87,0.2);
    border:1px solid var(--oliva);
    color:var(--hueso);
    font-size:0.9rem;
  }
  .vn-resultado strong{ font-family:var(--font-display); font-size:1.05rem; }
  .vn-resultado .vn-resultado-cambio{ font-family:var(--font-mono); font-size:1.3rem; font-weight:600; color:var(--oliva-claro); }
  .vn-resultado .vn-resultado-alerta{ color:var(--brasa-viva); margin-top:4px; }

  .vn-cobrar{ display:grid; grid-template-columns:1fr 1fr; gap:8px; }
  .vn-btn{
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.95rem;
    padding:12px 8px;
    border-radius:var(--radius);
    border:1px solid transparent;
    cursor:pointer;
    line-height:1.2;
  }
  .vn-btn small{ display:block; font-family:var(--font-mono); font-weight:500; font-size:0.72rem; opacity:0.8; }
  .vn-btn.principal{ background:var(--brasa); color:var(--hueso); }
  .vn-btn.principal:hover{ background:var(--brasa-viva); }
  .vn-btn.secundario{ background:var(--carbon-mas-alto); border-color:var(--carbon-borde); color:var(--hueso); }
  .vn-btn.secundario:hover{ border-color:var(--brasa-viva); }
  .vn-btn:disabled{ opacity:0.5; cursor:wait; }

  @media (max-width:1000px){
    .vn-layout{ grid-template-columns:1fr; height:auto; }
    .vn-grilla{ max-height:50vh; }
  }
</style>

<div class="main-panel">
  <div class="content-wrapper vn-contenido">

    <div class="vn-layout">

      <!-- Productos -->
      <section class="vn-productos" aria-label="Productos">
        <input type="search" class="vn-campo" id="vn-buscar" placeholder="Buscar producto (F2)" autocomplete="off">
        <div class="vn-categorias" id="vn-categorias"></div>
        <div class="vn-grilla" id="vn-grilla">
          <p class="vn-vacio">Cargando productos…</p>
        </div>
      </section>

      <!-- Pedido y cobro -->
      <aside class="vn-pedido" aria-label="Pedido">
        <div class="vn-titulo">
          <h2>Pedido</h2>
          <button type="button" class="vn-enlace" id="vn-vaciar">Vaciar (Esc)</button>
        </div>

        <div class="vn-resultado vn-oculto" id="vn-resultado" role="status"></div>

        <!-- Un clic: "Para llevar" o el número de mesa -->
        <div class="vn-mesas" id="vn-mesas" aria-label="Para llevar o número de mesa"></div>

        <ul class="vn-lineas" id="vn-lineas">
          <li class="vn-vacio">Elige productos de la izquierda.</li>
        </ul>

        <input type="text" class="vn-campo" id="vn-nota" maxlength="120" placeholder="Nota para cocina (opcional)" autocomplete="off">

        <div>
          <div class="vn-fila vn-oculto" id="vn-fila-subtotal"><span>Subtotal</span><b id="vn-subtotal">0.00</b></div>
          <div class="vn-fila vn-oculto" id="vn-fila-descuento">
            <span>Descuento (%) <input type="text" inputmode="decimal" class="vn-campo" id="vn-descuento" value="" style="display:inline-block;width:64px;padding:4px 8px;" aria-label="Porcentaje de descuento"></span>
            <b id="vn-descuento-monto">-0.00</b>
          </div>
          <div class="vn-fila vn-total"><span>Total</span><b><span id="vn-total">0.00</span> Bs</b></div>
          <button type="button" class="vn-enlace" id="vn-ver-descuento">Agregar descuento</button>
        </div>

        <div class="vn-segmento tres" id="vn-metodo">
          <button type="button" class="vn-opcion activo" data-metodo="efectivo">Efectivo</button>
          <button type="button" class="vn-opcion" data-metodo="qr">QR</button>
          <button type="button" class="vn-opcion" data-metodo="mixto">Mixto</button>
        </div>

        <div class="vn-par" id="vn-bloque-efectivo">
          <div class="vn-oculto" id="vn-bloque-qr">
            <label for="vn-qr">Monto por QR</label>
            <input type="text" inputmode="decimal" class="vn-campo" id="vn-qr" autocomplete="off">
          </div>
          <div>
            <label for="vn-recibido">Efectivo recibido</label>
            <input type="text" inputmode="decimal" class="vn-campo" id="vn-recibido" autocomplete="off">
          </div>
          <div class="vn-cambio" id="vn-caja-cambio"><span id="vn-cambio-texto">Cambio</span><b><span id="vn-cambio">0.00</span> Bs</b></div>
        </div>
        <div class="vn-info vn-oculto" id="vn-info-mixto">A cobrar en efectivo: <b id="vn-en-efectivo">0.00</b> Bs</div>
        <div class="vn-info vn-oculto" id="vn-bloque-solo-qr">Todo el pedido se cobra por QR. Confirma el pago en el celular antes de cobrar.</div>

        <div class="vn-aviso vn-oculto" id="vn-aviso" role="alert"></div>

        <div class="vn-cobrar">
          <button type="button" class="vn-btn principal" id="vn-cobrar-1">Cobrar · 1 ticket<small>F8</small></button>
          <button type="button" class="vn-btn secundario" id="vn-cobrar-2">Cobrar · 2 tickets<small>F9</small></button>
        </div>
      </aside>

    </div>

  </div>
  <!-- content-wrapper ends -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>

<?php footerAdmin($data); ?>
