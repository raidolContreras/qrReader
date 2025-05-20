<?php 
$users = FormsController::ctrGetUsersByRoute();
?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="content">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#routeModal">
        <i class="fas fa-plus"></i> Nueva punto de registro
    </button>
    <div class="table-responsive">
        <table id="routesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Encargado</th>
                    <th>Tipo de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal para nueva ruta -->
<div class="modal fade" id="routeModal" tabindex="-1" aria-labelledby="routeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routeModalLabel">Nueva punto de registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newRouteForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del punto de registro</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre del punto de acceso" required>
                    </div>
                    <div class="mb-3">
                      <label for="tipoRegistro" class="form-label">Tipo de registro</label>
                      <select id="tipoRegistro" name="tipoRegistro" class="form-control" required>
                        <option value="" disabled selected>Seleccione el tipo de registro</option>
                        <option value="1">Peatonal</option>
                        <option value="2">Vehicular</option>
                        <option value="3">Autobús escolar</option>
                      </select>
                    </div>
                    <div class="mb-3">
                        <label for="encargado" class="form-label">Encargado(s)</label>
                        <select id="encargado" name="encargado[]" multiple="multiple" class="form-control" required>
                            <!-- se llenan via JS -->
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo $user['nombre'] . ' ' . $user['apellidos'] . ' (' . $user['role'] . ')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar ruta -->
<div class="modal fade" id="editRouteModal" tabindex="-1" aria-labelledby="editRouteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editRouteModalLabel">Editar punto de registro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editRouteForm">
        <div class="modal-body">
          <input type="hidden" id="editRouteId" name="idRoute">
          <div class="mb-3">
            <label for="editNombre" class="form-label">Nombre del punto de registro</label>
            <input type="text" class="form-control" id="editNombre" name="nameRoute" required>
          </div>
          <div class="mb-3">
            <label for="editTipoRegistro" class="form-label">Tipo de registro</label>
            <select id="editTipoRegistro" name="tipoRegistro" class="form-control" required>
              <option value="" disabled selected>Seleccione el tipo de registro</option>
              <option value="1">Peatonal</option>
              <option value="2">Vehicular</option>
              <option value="3">Autobús escolar</option>
            </select>
          <div class="mb-3">
            <label for="editEncargado" class="form-label">Encargado(s)</label>
            <select id="editEncargado" name="encargado[]" multiple class="form-control" required>
                <!-- opciones PHP -->
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo $user['nombre'] . ' ' . $user['apellidos'] . ' (' . $user['role'] . ')'; ?></option>
                <?php endforeach; ?>
            </select>
          </div>
        </div><!-- /modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-success">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="assets/js/routes.js"></script>