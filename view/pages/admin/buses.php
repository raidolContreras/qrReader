<div class="content">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#busModal">
        <i class="fas fa-bus"></i> Nuevo Autobús
    </button>
    <div class="table-responsive">
        <table id="busesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número de Autobús</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal para crear nuevo autobús -->
<div class="modal fade" id="busModal" tabindex="-1" aria-labelledby="busModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="busModalLabel">Crear Autobús</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newBusForm">
                    <div class="mb-3">
                        <label class="form-label">Número de Autobús</label>
                        <input type="number" name="numero" id="numero" class="form-control" required min="1">
                    </div>
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar autobús -->
<div class="modal fade" id="editBusModal" tabindex="-1" aria-labelledby="editBusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBusModalLabel">Editar Autobús</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editBusForm">
                    <input type="hidden" name="id" id="editBusId">
                    <div class="mb-3">
                        <label class="form-label">Número de Autobús</label>
                        <input type="number" name="numero" id="editNumero" class="form-control" required min="1">
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
    const table = $('#busesTable').DataTable({
        ajax: {
            url: 'controller/selectAction.php',
            type: 'POST',
            data: { action: 'getBuses' },
            dataType: 'json',
            dataSrc: function(json) {
                // Adapt to the structure: { status, data: [...] }
                return json.data || [];
            }
        },
        columns: [
            { 
                data: null,
                render: (data, type, row, meta) => meta.row + 1
            },
            { data: 'numberBus' },
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
                            <button type="button" class="action-btn edit-btn tooltip-btn" data-id="${row.idBus}" data-tooltip="Editar autobús">
                                <i class="fas fa-edit"></i>
                            </button>
                    `;
                    if (row.isActive === 1) {
                        actionButtons += `
                            <button type="button" class="action-btn suspend-btn tooltip-btn" data-id="${row.idBus}" data-tooltip="Suspender autobús">
                                <i class="fas fa-ban"></i>
                            </button>
                        `;
                    } else if (row.isActive === 0) {
                        actionButtons += `
                            <button type="button" class="action-btn reactivate-btn tooltip-btn" data-id="${row.idBus}" data-tooltip="Reactivar autobús">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="action-btn delete-btn tooltip-btn" data-id="${row.idBus}" data-tooltip="Eliminar autobús">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                    }
                    actionButtons += `</div>`;
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

    // Crear autobús
    $('#newBusForm').on('submit', function(event) {
        event.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'newBus' });
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
                    $('#newBusForm')[0].reset();
                    $('#busModal').modal('hide');
                }
            },
            error: error => console.error(error)
        });
    });

    // Editar autobús
    $('#editBusForm').on('submit', function(event) {
        event.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'editBus' });
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    $('#editBusForm')[0].reset();
                    $('#editBusModal').modal('hide');
                    table.ajax.reload();
                }
            },
            error: error => console.error(error)
        });
    });

    // Botón editar
    $('#busesTable').on('click', '.edit-btn', function() {
        const busId = $(this).data('id');
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: { action: 'getBus', busId },
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    $('#editBusId').val(response.data.id);
                    $('#editNumero').val(response.data.numero);
                    $('#editBusModal').modal('show');
                }
            },
            error: error => console.error(error)
        });
    });

    // Botón suspender
    $('#busesTable').on('click', '.suspend-btn', function() {
        const busId = $(this).data('id');
        if (confirm('¿Estás seguro de suspender este autobús?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'suspendBus', busId },
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

    // Botón reactivar
    $('#busesTable').on('click', '.reactivate-btn', function() {
        const busId = $(this).data('id');
        if (confirm('¿Estás seguro de reactivar este autobús?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'reactivateBus', busId },
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

    // Botón eliminar
    $('#busesTable').on('click', '.delete-btn', function() {
        const busId = $(this).data('id');
        if (confirm('¿Estás seguro de eliminar este autobús?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'deleteBus', busId },
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
</script></div>