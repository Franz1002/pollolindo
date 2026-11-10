<?php headerAdmin($data);
getModal('modalInsumos', $data); ?>

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
              <h4 class="card-title mb-0">CATÁLOGO DE INSUMOS</h4>
              <button class="btn btn-primary" onclick="nuevoInsumo()">
                <i class="mdi mdi-plus"></i> Nuevo insumo
              </button>
            </div>
            <div class="table-responsive">
              <table id="tableInsumos" class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th>Opciones</th>
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
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori 2026</span>
    </div>
  </footer>
</div>
</div>

<?php footerAdmin($data); ?>
