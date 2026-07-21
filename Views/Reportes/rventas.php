<?php headerAdmin($data); ?>
<?php getModal('modalUsuarios', $data); ?>

<title>
  <?= $data['title']; ?>
</title>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title text-center">REPORTE DE VENTAS</h4>

        <!-- Formulario para generar reporte total de ventas -->
        <div class="row">
          <div class="form-group col-md-12">
            <form action="<?php echo BASE_URL; ?>/Reportes/reporteArqueoActual" method="POST" target="_blank" class="col-md-6">
              <div class="form-row form-default">
                <div class="form-group col-md-6">
                  <label class="float-label control-label" style="font-size: 17px;">Informe de arqueo actual</label>
                </div>
                <div class="form-group col-md-6">
                  <button type="submit" class="btn btn-outline-primary">Generar Reporte de Arqueo actual</button>
                </div>
              </div>
            </form>

            <form action="<?php echo BASE_URL; ?>/Reportes/reporteArqueoMensual" method="POST" target="_blank" class="col-md-12">
              <div class="form-row form-default">
                <div class="form-group col-md-4">
                  <label class="float-label control-label" style="font-size: 17px;">Desde:</label>
                  <input type="date" name="fechaInicio" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                  <label class="float-label control-label" style="font-size: 17px;">Hasta:</label>
                  <input type="date" name="fechaFin" class="form-control" required>
                </div>
                <div class="form-group col-md-4 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary">Generar Reporte Mensual</button>
                </div>
              </div>
            </form>

          </div>
        </div>


      

      </div>
    </div>
  </div>
  <!-- content-wrapper ends -->

  <!-- Footer -->
  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori 2025</span>
    </div>
  </footer>
  <!-- partial -->
</div>
<!-- main-panel ends -->

<?php footerAdmin($data); ?>