<!-- Modal -->
<div class="modal fade" id="modalFormUsuario" tabindex="-1" aria-labelledby="modalFormUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #13161c;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title text-center" id="modalFormUsuarioLabel">Nuevo Usuario</h5>
            </div>
            <div class="modal-body">
                <form id="formUsuario" name="formUsuario">
                    <input type="hidden" id="idUsuario" name="idUsuario" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellidos" class="">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ingrese los apellidos" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombres" class="">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Ingrese los nombres" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ci" class="">Carnet de Identidad</label>
                                <input type="text" class="form-control" id="ci" name="ci" placeholder="Ingrese el carnet de identidad" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular" class="">Celular</label>
                                <input type="number" class="form-control" id="celular" name="celular" placeholder="Ingrese el número de celular" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese el correo electrónico" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese la contraseña" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="listCargo" class="">Cargo del Usuario</label>
                                <select class="form-control" id="listCargo" name="cargo" required>
                                    <!-- Las opciones se cargarán aquí -->
                                </select>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado" class="">Estado</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button id="btnActionForm" onclick="registrarUsuario(event);" class="btn btn-primary" type="button">
                            <i class="fa fa-check-circle"></i><span id="btnText"> Registrar</span>
                        </button>&nbsp;&nbsp;&nbsp;
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Ver Usuario -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-labelledby="modalVerUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #337ab7;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title text-center text-white" id="modalVerUsuarioLabel" style="font-weight: bold; font-size: 20px;">Detalle del usuario</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-2" style="border: 4px solid #007bff; background: #bed8f4;">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px;">Nombre</h5>
                                <p class="card-text" id="verNombre"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2" style="border: 4px solid #007bff; background: #bed8f4;">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px;">Apellido</h5>
                                <p class="card-text" id="verApellido"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2" style="border: 4px solid #007bff; background: #bed8f4;">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px;">Correo</h5>
                                <p class="card-text" id="verEmail"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2" style="border: 4px solid #007bff; background: #bed8f4;">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px;">Cedula</h5>
                                <p class="card-text" id="verCedula"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2" style="border: 4px solid #007bff; background: #bed8f4;">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px;">Celular</h5>
                                <p class="card-text" id="verCelular"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2" style="border: 4px solid #007bff; background: #bed8f4;">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="font-size: 20px;">Tipo</h5>
                                <p class="card-text" id="verUsuario"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>