<?php headerAdmin($data);
getModal('modalUsuarios', $data); ?>

<style>
  /* Estilos propios del dashboard — usan los tokens ya cargados en <head> */
  .dcl-saludo{
    font-family:var(--font-display);
    font-weight:600;
    font-size:1.6rem;
    color:var(--hueso);
    margin:0 0 4px;
  }
  .dcl-subtitulo{
    color:var(--hueso-opaco);
    font-size:0.92rem;
    margin:0 0 28px;
  }

  .dcl-kpis{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-bottom:18px;
  }
  @media (max-width:700px){ .dcl-kpis{ grid-template-columns:1fr; } }

  .dcl-kpi{
    background:var(--carbon-alto);
    border:1px solid var(--carbon-borde);
    border-radius:10px;
    padding:22px 24px;
  }
  .dcl-kpi .etiqueta{
    font-family:var(--font-mono);
    font-size:0.72rem;
    text-transform:uppercase;
    letter-spacing:0.08em;
    color:var(--hueso-opaco);
    margin-bottom:10px;
  }
  .dcl-kpi .monto{
    font-family:var(--font-mono);
    font-size:2.1rem;
    color:var(--brasa-viva);
    font-weight:600;
    line-height:1;
  }
  .dcl-indicador{
    margin-top:14px;
    height:4px;
    border-radius:4px;
    background:var(--carbon-borde);
    overflow:hidden;
  }
  .dcl-indicador .relleno{
    height:100%;
    background:linear-gradient(90deg, var(--brasa-tenue), var(--brasa-viva));
  }
  .dcl-indicador.activo .relleno{
    animation:dcl-glow 2.2s ease-in-out infinite;
  }
  @keyframes dcl-glow{
    0%,100%{ box-shadow:0 0 6px 0 rgba(229,114,47,0.5); }
    50%{ box-shadow:0 0 14px 2px rgba(229,114,47,0.85); }
  }

  .dcl-panel{
    background:var(--carbon-alto);
    border:1px solid var(--carbon-borde);
    border-radius:10px;
    padding:24px;
    margin-bottom:18px;
  }
  .dcl-panel h2{
    font-family:var(--font-body);
    font-size:0.85rem;
    text-transform:uppercase;
    letter-spacing:0.06em;
    color:var(--hueso-tenue);
    margin:0 0 18px;
    font-weight:600;
  }

  /* Gráfica de barras (generada desde PHP con los datos reales del mes) */
  .dcl-grafica{ display:flex; align-items:flex-end; gap:6px; height:110px; }
  .dcl-barra{
    flex:1;
    background:linear-gradient(180deg, var(--brasa-viva), var(--brasa-tenue));
    border-radius:3px 3px 0 0;
    min-height:3px;
    position:relative;
  }
  .dcl-barra .valor{
    position:absolute;
    top:-18px;
    left:50%;
    transform:translateX(-50%);
    font-family:var(--font-mono);
    font-size:0.62rem;
    color:var(--hueso-opaco);
    white-space:nowrap;
  }
  .dcl-etiquetas-eje{ display:flex; gap:6px; margin-top:8px; }
  .dcl-etiquetas-eje span{
    flex:1;
    text-align:center;
    font-family:var(--font-mono);
    font-size:0.62rem;
    color:var(--hueso-opaco);
  }
  .dcl-vacio{
    color:var(--hueso-opaco);
    font-size:0.88rem;
    font-style:italic;
    text-align:center;
    padding:20px 0;
  }

  /* Alertas de insumos */
  .dcl-fila-insumo{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid var(--carbon-borde);
  }
  .dcl-fila-insumo:last-child{ border-bottom:none; }
  .dcl-fila-insumo .nombre{ font-size:0.9rem; color:var(--hueso); }
  .dcl-badge{
    font-family:var(--font-mono);
    font-size:0.68rem;
    text-transform:uppercase;
    letter-spacing:0.04em;
    padding:3px 9px;
    border-radius:20px;
  }
  .dcl-badge.agotado{ background:rgba(162,59,46,0.18); color:#E08579; }
  .dcl-badge.poca_cantidad{ background:rgba(199,81,31,0.16); color:var(--brasa-viva); }

  /* Accesos rápidos */
  .dcl-accesos{ display:flex; flex-wrap:wrap; gap:10px; }
  .dcl-acceso{
    font-family:var(--font-body);
    font-weight:600;
    font-size:0.88rem;
    padding:11px 18px;
    border-radius:6px;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    cursor:pointer;
    border:1px solid transparent;
  }
  .dcl-acceso.primario{ background:var(--brasa); color:var(--hueso); }
  .dcl-acceso.primario:hover{ background:var(--brasa-viva); color:var(--hueso); }
  .dcl-acceso.secundario{ background:transparent; color:var(--hueso); border-color:var(--carbon-borde); }
  .dcl-acceso.secundario:hover{ border-color:var(--hueso-opaco); color:var(--hueso); }
</style>

<div class="main-panel">
  <div class="content-wrapper">

    <h1 class="dcl-saludo">Hola, <?= $_SESSION['idUsuario']['nombre']; ?></h1>
    <p class="dcl-subtitulo">Así viene el negocio hoy.</p>

    <div class="dcl-kpis">
      <div class="dcl-kpi">
        <div class="etiqueta">Ventas de hoy</div>
        <div class="monto">Bs. <?= number_format($data['ventaHoy'], 2); ?></div>
        <div class="dcl-indicador <?= $data['ventaHoy'] > 0 ? 'activo' : ''; ?>">
          <div class="relleno" style="width: <?= $data['ventaHoy'] > 0 ? '100' : '0'; ?>%;"></div>
        </div>
      </div>
      <div class="dcl-kpi">
        <div class="etiqueta">Ventas del mes</div>
        <div class="monto">Bs. <?= number_format($data['ventaMes'], 2); ?></div>
      </div>
    </div>

    <div class="dcl-panel">
      <h2>Últimos 14 días</h2>
      <?php if (empty($data['graficoDias'])): ?>
        <div class="dcl-vacio">Todavía no hay ventas registradas este mes.</div>
      <?php else: ?>
        <div class="dcl-grafica">
          <?php foreach ($data['graficoDias'] as $fecha => $monto):
            $alturaPct = $data['graficoMax'] > 0 ? max(4, ($monto / $data['graficoMax']) * 100) : 4;
          ?>
            <div class="dcl-barra" style="height: <?= $alturaPct; ?>%;">
              <span class="valor"><?= number_format($monto, 0); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="dcl-etiquetas-eje">
          <?php foreach ($data['graficoDias'] as $fecha => $monto): ?>
            <span><?= substr($fecha, 0, 6); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="dcl-panel">
      <h2>Alertas de insumos <?= ($data['totalAgotados'] + $data['totalPorAgotar']) > 0 ? '(' . ($data['totalAgotados'] + $data['totalPorAgotar']) . ')' : ''; ?></h2>
      <?php if (empty($data['alertasInsumos'])): ?>
        <div class="dcl-vacio">Todo el inventario está en buen nivel.</div>
      <?php else: ?>
        <?php foreach ($data['alertasInsumos'] as $item): ?>
          <div class="dcl-fila-insumo">
            <span class="nombre"><?= $item['producto']; ?></span>
            <span class="dcl-badge <?= $item['estado_css']; ?>"><?= $item['estado']; ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="dcl-panel">
      <h2>Accesos rápidos</h2>
      <div class="dcl-accesos">
        <a class="dcl-acceso primario" href="<?= base_url(); ?>/ventas">Nueva venta</a>
        <a class="dcl-acceso secundario" href="<?= base_url(); ?>/inventario">Ver inventario</a>
        <a class="dcl-acceso secundario" href="<?= base_url(); ?>/reportes/rventas">Reporte de ventas</a>
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

<?php footerAdmin($data); ?>
