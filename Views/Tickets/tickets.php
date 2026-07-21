<?php headerAdmin($data);
getModal('modalUsuarios', $data); ?>


<title>
  <?= $data['title']; ?>
</title>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title text-center">LISTA DE TICKETS</h4>

        <div class="table-responsive">
          <table  id="tableTickets" class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Pedido</th>
                <th>Emitido por</th>
                <th>Fecha emitido</th>
                <th>Descuento</th>
                <th>Total venta</th>             
                <th>Ticket</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
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