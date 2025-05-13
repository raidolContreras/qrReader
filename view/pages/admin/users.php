<div class="content">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </button>
    <div class="table-responsive">
        <table id="usersTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal para crear nuevo usuario -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newUserForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" id="role" class="form-select">
                            <option value="admin">Administrador</option>
                            <option value="usuario">Escáner QR</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" name="id" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="editNombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" id="editApellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" id="editRole" class="form-select">
                            <option value="admin">Administrador</option>
                            <option value="usuario">Escáner QR</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(() => {
    // Instancia de DataTable
    const table = $('#usersTable').DataTable({
        ajax: {
            url: 'controller/selectAction.php',
            type: 'POST',
            data: { action: 'getUsers' },
            dataType: 'json',
            dataSrc: '' // El array está en la raíz del JSON
        },
        columns: [
            { 
                data: null,
                render: data => `${data.nombre} ${data.apellidos}`
            },
            { data: 'email' },
            { 
                data: 'role',
                render: role => {
                    if (role === 'admin') {
                        return '<span class="badge bg-primary">Administrador</span>';
                    } else if (role === 'usuario') {
                        return '<span class="badge bg-secondary">Escáner QR</span>';
                    }
                    return '<span class="badge bg-dark">Desconocido</span>';
                }
            },
            {
                data: null,
                render: (data, type, row) => `
                    <div class="action-buttons">
                        <button type="button" class="action-btn edit-btn tooltip-btn" data-id="${row.id}" data-tooltip="Editar registro">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button type="button" class="action-btn delete-btn tooltip-btn" data-id="${row.id}" data-tooltip="Eliminar registro">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </div>
                `
            }
        ],
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ filas",
            info: "Mostrando _START_ a _END_ de _TOTAL_ filas",
            infoEmpty: "Mostrando 0 a 0 de 0 filas",
            infoFiltered: "(filtrado de _MAX_ total filas)",
            zeroRecords: "No se encontraron resultados"
        }
    });

    // Handler para el envío del formulario de nuevo usuario
    $('#newUserForm').on('submit', function(event) {
        event.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'newUser' });
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    table.ajax.reload();
                    $('#newUserForm')[0].reset();
                    $('#userModal').modal('hide');
                }
            },
            error: error => console.error(error)
        });
    });

    // Handler para el envío del formulario de edición
    $('#editUserForm').on('submit', function(event) {
        event.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'editUser' });
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    $('#editUserForm')[0].reset();
                    $('#editUserModal').modal('hide');
                    table.ajax.reload();
                }
            },
            error: error => console.error(error)
        });
    });

    // Manejador para el botón editar (delegado)
    $('#usersTable').on('click', '.edit-btn', function() {
        const userId = $(this).data('id');
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: { action: 'getUser', userId },
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    $('#editUserId').val(response.data.id);
                    $('#editNombre').val(response.data.nombre);
                    $('#editApellidos').val(response.data.apellidos);
                    $('#editEmail').val(response.data.email);
                    $('#editRole').val(response.data.role);
                    $('#editUserModal').modal('show');
                }
            },
            error: error => console.error(error)
        });
    });

    // Manejador para el botón eliminar (delegado)
    $('#usersTable').on('click', '.delete-btn', function() {
        const userId = $(this).data('id');
        if (confirm('¿Estás seguro de eliminar este registro?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'deleteUser', userId },
                dataType: 'json',
                success: response => {
                    if (response.error) {
                        alert(response.error);
                    } else {
                        table.ajax.reload();
                    }
                },
                error: error => console.error(error)
            });
        }
    });
});
</script>
