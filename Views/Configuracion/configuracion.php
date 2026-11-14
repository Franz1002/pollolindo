<?php headerAdmin($data); ?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-lg-6 col-md-8 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Configuración</h4>

            <form id="formConfiguracion" autocomplete="off">
              <div class="form-group">
                <label for="numero_mesas">Cantidad de mesas</label>
                <input type="number" class="form-control" id="numero_mesas" name="numero_mesas"
                  min="1" max="<?= (int) $data['max_mesas']; ?>" step="1"
                  value="<?= (int) $data['numero_mesas']; ?>" required style="max-width: 160px;">
                <small class="text-muted">
                  Son los botones de mesa que aparecen en Vender (del 1 hasta este número).
                  Máximo <?= (int) $data['max_mesas']; ?>.
                </small>
              </div>

              <button type="submit" class="btn btn-primary" id="btnGuardarConfiguracion">Guardar cambios</button>
            </form>
          </div>
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
