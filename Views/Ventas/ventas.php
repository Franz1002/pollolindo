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



            <div class="container-fluid">
              <!-- Primera fila: Menú, Bebidas, Combos y Pagos -->
              <div class="row g-3">
                <!-- Menú -->
                <div class="col-md-3">
                  <!-- Vista: menuVista.php -->
                  <div class="menu-section">
                    <h4>Seleccione Menú</h4>
                    <div class="scroll-container" id="menu-container">
                      <!-- Aquí se insertarán los productos dinámicamente -->
                    </div>
                  </div>
                </div>
                <!-- Bebidas -->
                <div class="col-md-3">
                  <div class="menu-section">
                    <h4>Seleccione Bebida</h4>
                    <div class="scroll-container" id="bebida-container">
                      <!-- Aquí se insertarán los productos dinámicamente -->
                    </div>
                  </div>
                </div>
                <!-- Combos -->
                <div class="col-md-3">
                  <div class="menu-section">
                    <h4>Seleccione Combo</h4>
                    <div class="scroll-container" id="combo-container">
                      <!-- Aquí se insertarán los productos dinámicamente -->
                    </div>
                  </div>
                </div>

                <!-- Pagos -->
                <div class="col-md-3">
                  <div class="menu-section-cobrar">
                    <h4>Total a Cobrar</h4>
                    <div class="mb-3">
                      <h5>Subtotal: <span id="subtotal">0.00</span> Bs.</h5>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Efectivo Recibido:</label>

                      <input type="number" class="form-control" id="efectivo-recibido" min="0" onchange="calcularCambio()">


                    </div>
                    <button style="font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; color:#000;" class="btn btn-warning btn-lg w-100 mb-2" onclick="calcularCambio()">Calcular Cambio</button>
                    <div class="mb-3">
                      <h5>Cambio: <span id="cambio" style="font-size:25px;">0.00</span> <span id="moneda" style="font-size:25px;"> Bs.</span></h5>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Descuento:</label>
                      <input type="number" class="form-control" id="descuento" min="0" max="100" value="0"
                        onchange="aplicarDescuento()">
                    </div>
                    <button style="color: #000;" class="btn btn-danger btn-lg w-100 mb-2" onclick="aplicarDescuento()">Aplicar Descuento</button>
                    <hr>
                    <div class="mb-3" style="margin-top: -28px;">
                      <h5 class="text-center">Total Final</h5>
                      <h3 class="text-center"><span id="total-final">0.00</span> Bs.</h3>
                      <button style="font-size:16px; color:#000;" class="btn-inverse-danger for inverse buttons btn-lg w-100 mt-3" onclick="finalizarVenta()">FINALIZAR IMPRIMIR <i class="mdi mdi-printer btn-icon-append"></i></button>

                      <button style="font-size:16px; color:#000;" class="btn-inverse-warning for inverse buttons btn-lg w-100 mt-3" onclick="finalizarVentaDoble()">IMPRIMIR 2 TICKETS <i class="mdi mdi-printer btn-icon-append"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sección inferior -->
              <div class="row g-3 mt-2">
                <div class="col-md-3">
                  <div class="control-section">
                    <h4>Cliente</h4>
                    <input type="text" class="form-control" id="nombre-cliente" placeholder="Nombre del cliente"><br>
                    <input type="text" class="form-control" id="numero-cliente" placeholder="celular del cliente">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="control-section">
                    <h4>Mesa</h4>
                    <input type="text" class="form-control" id="numero-mesa" placeholder="Número de mesa">
                    <br>
                    <button style="font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; color:#000;" class="btn btn-danger btn-lg w-100">Llevar</button>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="control-section">
                    <h4>Productos seleccionados</h4>
                    <ul id="productosSeleccionados" class="list-group productos-list">
                      <!-- Los productos seleccionados se mostrarán aquí -->
                    </ul>
                  </div>
                </div>
                <!-- Formulario oculto para enviar los datos de la venta -->
                <form id="ventaForm" style="display: none;">
                  <input type="hidden" id="venta_id">
                  <input type="hidden" id="cliente_id">
                  <input type="hidden" id="numero_mesa">
                  <input type="hidden" id="productos_data"> <!-- Productos seleccionados -->
                  <input type="hidden" id="total_final">
                  <input type="hidden" id="descuento">
                </form><br><br>
              </div>

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