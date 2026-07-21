<?php headerAdmin($data);
getModal('modalCategorias', $data); ?>


<title>
  <?= $data['title']; ?>
</title>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row ">
      <div class="col-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title text-center">LISTA DE CATEGORIAS</h4>
            <button style="width: 30%; height:57px;  display: flex; align-items: center; justify-content: center;" type="button" class="btn btn-dark btn-lg btn-block" onclick="openModalCategoria();">
              <i class="mdi mdi-account-multiple-plus icon-md" style="margin-right: 8px;"></i>
              REGISTRAR NUEVA CATEGORIA
            </button>
            <div class="table-responsive">
              <table id="tableCategorias" class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
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
  <!-- partial:<?= base_url_template(); ?>/partials/_footer.html -->
  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori 2025</span>
    </div>
  </footer>
  <!-- partial -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>

<?php footerAdmin($data); ?>