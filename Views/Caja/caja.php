<?php headerAdmin($data);
getModal('modalCaja', $data); ?>

<title><?= $data['title']; ?></title>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">CONTROL DE CAJA</h4>
                            <button class="btn btn-outline-secondary" onclick="reentrenarModelo()">
                                <i class="mdi mdi-brain"></i> Reentrenar modelo IA
                            </button>
                        </div>

                        <div id="panelCajaCerrada" style="display:none;">
                            <p>No tienes ninguna caja abierta en este momento.</p>
                            <button class="btn btn-primary" onclick="abrirCaja()">
                                <i class="mdi mdi-lock-open-variant"></i> Aperturar caja
                            </button>
                        </div>

                        <div id="panelCajaAbierta" style="display:none;">
                            <div class="row align-items-center">
                                <div class="col-md-3"><strong>Abierta desde:</strong><br><span
                                        id="cajaFechaApertura"></span></div>
                                <div class="col-md-3"><strong>Ventas registradas:</strong><br><span
                                        id="cajaCantidadTickets"></span></div>
                                <div class="col-md-3"><strong>Efectivo esperado (en vivo):</strong><br>Bs. <span
                                        id="cajaEfectivoEsperado"></span></div>
                                <div class="col-md-3">
                                    <button class="btn btn-danger" onclick="abrirModalCierreCaja()">
                                        <i class="mdi mdi-lock"></i> Cerrar caja
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">HISTORIAL DE CIERRES</h4>
                        <div class="table-responsive">
                            <table id="tableCaja" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Responsable</th>
                                        <th>Fecha apertura</th>
                                        <th>Fecha cierre</th>
                                        <th>Esperado (Bs)</th>
                                        <th>Declarado (Bs)</th>
                                        <th>Diferencia (Bs)</th>
                                        <th>Resultado</th>
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
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Ariel Cori
                2025</span>
        </div>
    </footer>
</div>
</div>

<?php footerAdmin($data); ?>