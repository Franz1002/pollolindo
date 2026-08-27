<?php headerAdmin($data); ?>

<title>
  <?= $data['title']; ?>
</title>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">HISTORIAL DE COMPRAS</h4>
              <a href="<?= base_url(); ?>/compras" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Nueva compra
              </a>
            </div>
            <div class="table-responsive">
              <table id="tableCompras" class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Insumo</th>
                    <th>Cantidad</th>
                    <th>Costo total (Bs.)</th>
                    <th>Costo unitario (Bs.)</th>
                    <th>Fecha</th>
                    <th>Registrado por</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori 2025</span>
    </div>
  </footer>
</div>
</div>

<?php footerAdmin($data); ?>
