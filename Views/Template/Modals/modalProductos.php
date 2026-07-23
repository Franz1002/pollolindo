<!-- Modal para Registrar Nuevo Menú -->
<div class="modal fade" id="modalFormMenu" tabindex="-1" aria-labelledby="modalFormMenuLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #13161c;">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title text-center" id="modalFormMenuLabel">Nuevo Menú</h5>
            </div>
            <div class="modal-body">
                <form id="formMenu" name="formMenu" enctype="multipart/form-data">

                    <input type="hidden" id="idMenu" name="idMenu" value="">
                    <input type="hidden" id="imgActual" name="imgActual" value="">

                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_menu">Nombre del Menú</label>
                                <input type="text" class="form-control" id="nombre_menu" name="nombre_menu" placeholder="Ingrese el nombre del menú" required>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_menu">Precio del Menú</label>
                                <input type="number" class="form-control" id="precio_menu" name="precio_menu" placeholder="Ingrese el precio" step="0.01" required>
                            </div>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoria_id" class="form-label">Categoría</label>
                                <select id="categoria_id" name="categoria_id" class="form-select" required></select>
                            </div>
                        </div>

                        <!-- Detalle -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="detalle_menu">Detalle del Menú</label>
                                <textarea class="form-control" id="detalle_menu" name="detalle_menu" placeholder="Ingrese el detalle del menú" required></textarea>
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="img_menu">Imagen del Menú</label>
                                <input type="file" class="form-control" id="img_menu" name="img_menu">
                            </div>
                            <div class="form-group mt-2 text-center">
                                <img id="imgPreview" src="" alt="Vista previa de imagen" style="max-width: 100%; height: auto; display: none; border-radius: 10px;">
                            </div>



                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button id="btnActionFormMenu" onclick="guardarMenu(event);" class="btn btn-primary" type="button">
                            <i class="fa fa-check-circle"></i><span id="btnText"> Registrar</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>




</body>


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
<div class="modal fade" id="modalVerProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header custom-background-color" style="background: #337ab7;">
                <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center text-white" style="font-weight: bold; font-size: 20px;">Detalle del producto</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Nombre del producto</h5>
                                <p class="card-text font-weight-bold" id="verNombre"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Marca</h5>
                                <p class="card-text font-weight-bold" id="verMarca"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Cantidad</h5>
                                <p class="card-text font-weight-bold" id="verCantidad"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Precio</h5>
                                <p class="card-text font-weight-bold" id="verPrecio"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Categoria</h5>
                                <p class="card-text font-weight-bold" id="verCategoria"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Producto registrado por</h5>
                                <p class="card-text font-weight-bold" id="verUsuario"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px; font-weight:bold;">Descripcion</h5>
                                <p class="card-text font-weight-bold" id="verDescripcion"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-2 custom-card">
                            <div class="card-body" style="margin-left: 5px;">
                                <h5 class="card-title font-weight-bold text-center" style="font-size: 20px; font-weight:bold;">Imagen</h5>
                                <img class="card-text" id="verImagen" alt="Imagen del producto" style="width: 100%; height: auto;">
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>