<?php headerAdmin($data);
getModal('modalUsuarios', $data); ?>


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
            <h4 class="card-title text-center">LISTA DE USUARIOS</h4>
            <button style="width: 30%; height:57px;  display: flex; align-items: center; justify-content: center;" type="button" class="btn btn-dark btn-lg btn-block" onclick="openModalUsuario();">
              <i class="mdi mdi-account-multiple-plus icon-md" style="margin-right: 8px;"></i>
              REGISTRAR NUEVO USUARIO
            </button>
            <br><br>
            <div class="table-responsive">
              <table id="tableUsuarios" class="table table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Email</th>
                    <th>Celular</th>
                    <th>Cargo</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Jacob</td>
                    <td>Photoshop</td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td>
                      <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </td>
                    <td><label class="badge badge-danger">Pending</label></td>
                  </tr>
                  <tr>
                    <td>Messsy</td>
                    <td>Flash</td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td><label class="badge badge-warning">In progress</label></td>
                  </tr>
                  <tr>
                    <td>John</td>
                    <td>Premier</td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td><label class="badge badge-info">Fixed</label></td>
                  </tr>

                  <tr>
                    <td>Peter</td>
                    <td>After effects</td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td><label class="badge badge-success">Completed</label></td>
                  </tr>
                  <tr>
                    <td>Dave</td>
                    <td>53275535</td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td class="text-danger"> 28.76% <i class="mdi mdi-arrow-down"></i></td>
                    <td><label class="badge badge-warning">In progress</label></td>
                  </tr>
                </tbody>
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