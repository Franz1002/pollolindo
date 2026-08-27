<div class="modal fade" id="modalFormInsumo" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #13161c;">
            <div class="modal-header headerUpdate custom-background-color">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title text-center" id="tituloInsumoModal">Nuevo insumo</h5>
            </div>

            <div class="modal-body">
                <form id="formInsumo" name="formInsumo" onsubmit="guardarInsumo(event)">
                    <input type="hidden" id="idInsumo" name="id_insumo" value="">

                    <div class="mb-3">
                        <label>Nombre del insumo</label>
                        <input type="text" id="nombreInsumo" name="nombre_insumo" class="form-control" placeholder="Ej. Huevo, Chorizo, Salchicha" required>
                    </div>

                    <div class="mb-3">
                        <label>Tipo</label>
                        <select id="tipoInsumo" name="tipo_insumo" class="form-control" required>
                            <option value="pollo">Pollo</option>
                            <option value="bebida">Bebida</option>
                            <option value="otro" selected>Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Unidad de medida</label>
                        <input type="text" id="unidadInsumo" name="unidad_medida" class="form-control" placeholder="Ej. presa, porcion, unidad" required>
                    </div>

                    <div class="mb-3" id="filaEstadoInsumo" style="display:none;">
                        <label>Estado</label>
                        <select id="estadoInsumo" name="estado" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <div class="text-center mt-3">
                        <button id="btnActionFormInsumo" class="btn btn-primary" type="submit">
                            <i class="fa fa-check-circle"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
