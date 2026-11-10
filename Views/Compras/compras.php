<?php headerAdmin($data); ?>

<title>
  <?= $data['title']; ?>
</title>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-12 col-md-8 offset-md-2 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title text-center">REGISTRAR COMPRA DE INSUMO</h4>

            <form id="formCompra" name="formCompra" onsubmit="registrarCompra(event)">
              <div class="mb-3">
                <label>Insumo</label>
                <select id="insumoCompra" name="insumo_id" class="form-control" required>
                  <option value="" disabled selected>Seleccione un insumo</option>
                </select>
                <small id="stockActualInfo" class="text-muted"></small>
              </div>

              <div class="mb-3">
                <label>Cantidad comprada</label>
                <input type="number" step="0.01" min="0.01" id="cantidadCompra" name="cantidad" class="form-control"
                  placeholder="Ej. 8 (pollos), 24 (bebidas)" required>
              </div>

              <div class="mb-3">
                <label>Costo total de la compra (Bs.)</label>
                <input type="number" step="0.01" min="0.01" id="costoTotalCompra" name="costo_total"
                  class="form-control" placeholder="Lo que pagaste en total, no por unidad" required>
              </div>

              <div class="mb-3">
                <label>Fecha de compra</label>
                <input type="date" id="fechaCompra" name="fecha_compra" class="form-control" required>
              </div>

              <div class="text-center mt-4">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-check-circle"></i> Registrar compra
                </button>
                <a href="<?= base_url(); ?>/compras/verCompras" class="btn btn-outline-info"> Ver historial de compras
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori 2026</span>
    </div>
  </footer>
</div>
</div>

<?php footerAdmin($data); ?>