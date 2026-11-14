<div class="modal fade" id="modalFormCategoria" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="background-color: #13161c;">
            <div class="modal-header headerUpdate custom-background-color">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title text-center" id="titleModal">Nueva Categoría</h5>
            </div>

            <div class="modal-body">
                <form id="formCategoria" name="formCategoria" onsubmit="registrarCategoria(event)">
                    <input type="hidden" id="idCategoria" name="idCategoria" value="">

                    <label for="nombre_categoria">Nombre de la categoría</label>
                    <input type="text" class="form-control animated" id="nombre_categoria" name="nombre" placeholder="Ingrese el nombre de la categoría" required>

                    <label for="descripcion_categoria">Descripción</label>
                    <textarea class="form-control animated" id="descripcion_categoria" name="descripcion" placeholder="Ingrese una descripción" required></textarea>

                    <div class="text-center">
                        <button id="btnActionForm" class="btn btn-primary" type="submit">
                            <i class="fa fa-check-circle"></i><span id="btnText"> Registrar</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<style>
    .custom-card {
        border: 4px solid #007bff;
        /* Ajusta el ancho del borde y el color según sea necesario */
        border-radius: 0.45rem;
        /* Ajusta el radio de borde según sea necesario */
        margin-bottom: 2rem;
        /* Margen inferior para separar las cards */
        background: #bed8f4;
        /* Ajusta el color de fondo de la card según sea necesario */
    }
</style>

<div class="modal fade" id="modalVerCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header custom-background-color" style="background: #337ab7;">
                <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center text-white" style="font-weight: bold; font-size: 20px;">Detalle de la Categoría</h4>
            </div>
            <div class="modal-body">
                <div class="card mb-6 custom-card">
                    <div class="card-body" style="margin-left: 5px;">
                        <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Categoría</h5>
                        <p class="card-text font-weight-bold" id="verCategoria"></p>
                    </div>
                </div>
                <div class="card mb-6 custom-card">
                    <div class="card-body" style="margin-left: 5px;">
                        <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Descripción</h5>
                        <p class="card-text font-weight-bold" id="verDescripcion"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>