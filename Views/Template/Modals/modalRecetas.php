<div class="modal fade" id="modalFormReceta" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color: #13161c;">
            <div class="modal-header headerUpdate custom-background-color">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title text-center" id="tituloRecetaModal">Receta</h5>
            </div>

            <div class="modal-body">
                <form id="formReceta" name="formReceta" onsubmit="guardarReceta(event)">
                    <input type="hidden" id="recetaIdMenu" name="id_menu" value="">

                    <div id="filasReceta"></div>

                    <button type="button" class="btn btn-outline-info btn-sm mt-2" onclick="agregarFilaReceta()">
                        <i class="mdi mdi-plus"></i> Agregar insumo
                    </button>

                    <div class="text-center mt-3">
                        <button id="btnActionFormReceta" class="btn btn-primary" type="submit">
                            <i class="fa fa-check-circle"></i> Guardar receta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
