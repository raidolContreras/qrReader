<div class="content">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </button>
    <div class="table-responsive">
        <table id="usersTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select">
                            <option value="admin">Admin</option>
                            <option value="moderador">Moderador</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "data": [],
            "columns": [{
                    "data": "nombre"
                },
                {
                    "data": "apellidos"
                },
                {
                    "data": "email"
                },
                {
                    "data": "role"
                },
                {
                    "data": null,
                    "render": function() {
                        return '<button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button> ' +
                            '<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                    }
                }
            ]
        });
    });
</script>