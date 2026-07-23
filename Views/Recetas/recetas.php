<?php headerAdmin($data);
getModal('modalRecetas', $data); ?>

<title>
  <?= $data['title']; ?>
</title>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title text-center">RECETAS POR PLATO</h4>
            <div class="table-responsive">
              <table id="tableRecetas" class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Plato</th>
                    <th>Precio</th>
                    <th>Receta</th>
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
  <!-- content-wrapper ends -->
  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori 2025</span>
    </div>
  </footer>
</div>
<!-- main-panel ends -->
</div>

<?php footerAdmin($data); ?>
