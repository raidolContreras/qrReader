<div class="content">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </button>
    <div class="table-responsive">
        <table id="usersTable" class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal para crear nuevo usuario -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                            <option value="coordinador">Coordinador</option>
                            <option value="chofer">Chofer</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                            <option value="coordinador">Coordinador</option>
                            <option value="chofer">Chofer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar</button>
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
                render: (data, type, row, meta) => meta.row + 1
            },
            { 
                data: null,
                render: data => `${data.nombre} ${data.apellidos}`
            },
            { data: 'email' },
            { 
                data: 'role',
                render: role => {
                    if (role === 'admin') {
                        return '<span class="badge bg-success">Administrador</span>';
                    } else if (role === 'coordinador') {
                        return '<span class="badge bg-secondary">Coordinador</span>';
                    } else if (role === 'chofer') {
                        return '<span class="badge bg-secondary">Chofer</span>';
                    }
                    return '<span class="badge bg-dark">Desconocido</span>';
                }
            },
            { 
                data: 'isActive',
                render: isActive => {
                    if (isActive === 1) {
                        return '<span class="badge bg-success">Activo</span>';
                    } else if (isActive === 0) {
                        return '<span class="badge bg-danger">Inactivo</span>';
                    }
                    return '<span class="badge bg-dark">Desconocido</span>';
                }
            },
            {
                data: null,
                render: (data, type, row) => {
                    let actionButtons = `
                        <div class="action-buttons">
                            <button type="button" class="action-btn edit-btn tooltip-btn" data-id="${row.id}" data-tooltip="Editar registro">
                                <i class="fas fa-edit"></i>
                            </button>
                    `;

                    if (row.isActive === 1) {
                        actionButtons += `
                            <button type="button" class="action-btn suspend-btn tooltip-btn" data-id="${row.id}" data-tooltip="Suspender usuario">
                                <i class="fas fa-user-slash"></i>
                            </button>
                            <button type="button" class="action-btn reset-password-btn tooltip-btn" data-id="${row.id}" data-tooltip="Reestablecer contraseña">
                                <i class="fas fa-key"></i>
                            </button>
                        `;
                    } else if (row.isActive === 0) {
                        actionButtons += `
                            <button type="button" class="action-btn reactivate-btn tooltip-btn" data-id="${row.id}" data-tooltip="Reactivar usuario">
                                <i class="fas fa-user-check"></i>
                            </button>
                            <button type="button" class="action-btn delete-btn tooltip-btn" data-id="${row.id}" data-tooltip="Eliminar registro">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                    }

                    actionButtons += `
                        </div>
                    `;
                    return actionButtons;
                }
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

    // Manejador para el botón suspender (delegado)
    $('#usersTable').on('click', '.suspend-btn', function() {
        const userId = $(this).data('id');
        if (confirm('¿Estás seguro de suspender este registro?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'suspendUser', userId },
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

    // Manejador para el botón reactivar (delegado)
    $('#usersTable').on('click', '.reactivate-btn', function() {
        const userId = $(this).data('id');
        if (confirm('¿Estás seguro de reactivar este registro?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'reactivateUser', userId },
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

    // Manejador para el botón reestablecer contraseña (delegado)
    $('#usersTable').on('click', '.reset-password-btn', function() {
        const userId = $(this).data('id');
        const resetPasswordModal = `
            <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="resetPasswordModalLabel">Reestablecer Contraseña</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="resetPasswordForm">
                                <input type="hidden" name="id" value="${userId}">
                                <div class="mb-3">
                                    <label class="form-label">Nueva Contraseña</label>
                                    <input type="password" name="password" id="resetPassword" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Repetir Contraseña</label>
                                    <input type="password" name="repeat_password" id="repeatPassword" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success">Aceptar</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Eliminar si ya existe el modal
        $('#resetPasswordModal').remove();
        $('body').append(resetPasswordModal);
        $('#resetPasswordModal').modal('show');

        // Handler para el formulario de reset
        $('#resetPasswordForm').on('submit', function(event) {
            event.preventDefault();
            const password = $('#resetPassword').val();
            const repeatPassword = $('#repeatPassword').val();
            // Validación de coincidencia de contraseñas y formato
            if ($('#resetPasswordError').length === 0) {
                $('<div id="resetPasswordError" class="text-danger mt-1"></div>')
                    .insertAfter($('#repeatPassword'));
            }
            let errorMsg = '';
            if (password !== repeatPassword) {
                errorMsg = 'Las contraseñas no coinciden.';
            } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password)) {
                errorMsg = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.';
            }
            if (errorMsg) {
                $('#resetPasswordError').text(errorMsg);
                return;
            } else {
                $('#resetPasswordError').remove();
            }
            const formData = $(this).serializeArray();
            formData.push({ name: 'action', value: 'resetPassword' });
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: response => {
                    if (response.error) {
                        alert(response.error);
                    } else {
                        $('#resetPasswordModal').modal('hide');
                    }
                },
                error: error => console.error(error)
            });
        });
    });
});
</script>
