<?php headerAdmin($data); ?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Tickets</h4>

        <!-- Filtro por fechas: por defecto, los tickets de hoy -->
        <div class="d-flex flex-wrap align-items-end mb-3" style="gap: 12px;">
          <div class="form-group mb-0">
            <label for="tk-desde">Desde</label>
            <input type="date" class="form-control" id="tk-desde" value="<?= $data['hoy']; ?>" max="<?= $data['hoy']; ?>">
          </div>
          <div class="form-group mb-0">
            <label for="tk-hasta">Hasta</label>
            <input type="date" class="form-control" id="tk-hasta" value="<?= $data['hoy']; ?>" max="<?= $data['hoy']; ?>">
          </div>
          <button type="button" class="btn btn-dark" id="tk-hoy" data-hoy="<?= $data['hoy']; ?>">Hoy</button>
          <span class="ml-auto" id="tk-resumen" style="font-family: var(--font-mono); color: var(--hueso);"></span>
        </div>
        <div class="text-danger mb-2" id="tk-aviso" role="alert"></div>
        <p class="text-muted mb-2" style="font-size: 0.85rem;">
          Solo se puede anular una venta mientras la caja en la que se cobró siga abierta.
          Las anuladas se muestran tachadas y no suman en el total.
        </p>

        <div class="table-responsive">
          <table id="tableTickets" class="table table-hover tabla-fluida" style="width: 100%;">
            <thead>
              <tr>
                <th>Pedido</th>
                <th>Fecha</th>
                <th>Productos</th>
                <th>Destino</th>
                <th>Pago</th>
                <th>Total</th>
                <th>Cajero</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
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
