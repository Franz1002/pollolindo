<?php headerAdmin($data);
getModal('modalUsuarios', $data); ?>


<title>Prueba de Impresión Epson</title>


<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row ">
      <div class="col-12 grid-margin">
        <div class="card">
          <div class="card-body">


            <div class="card-body">
              <h1>Prueba de Impresión Epson TM-T20III</h1>
              <button onclick="window.location.href='<?= base_url(); ?>/inicio/imprimir'">Imprimir</button>
            </div>
            <div>
           <h1>
            hola mundo
           </h1>
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