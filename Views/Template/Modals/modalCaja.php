<div class="modal fade" id="modalCierreCaja" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #13161c;">
            <div class="modal-header headerUpdate custom-background-color">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title text-center">Cerrar caja</h5>
            </div>
            <div class="modal-body">
                <form id="formCierreCaja" name="formCierreCaja" onsubmit="cerrarCaja(event)">
                    <p>Efectivo esperado según ventas del sistema: <strong>Bs. <span id="montoEfectivoEsperadoModal">0.00</span></strong></p>
                    <div class="mb-3">
                        <label>Efectivo contado en caja</label>
                        <input type="number" step="0.01" min="0" id="efectivoDeclarado" name="efectivo_declarado" class="form-control" required>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-check-circle"></i> Confirmar cierre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>