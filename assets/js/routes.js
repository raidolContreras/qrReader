
$(document).ready(() => {

    // Inicializamos Select2
    $('#encargado, #editEncargado').select2({
        placeholder: "Selecciona los usuarios responsables",
        allowClear: true,
        width: '100%'
    });

    // Instancia de DataTable
    const table = $('#routesTable').DataTable({
        ajax: {
            url: 'controller/selectAction.php',
            type: 'POST',
            data: { action: 'getRoutes' },
            dataType: 'json',
            dataSrc: function (json) {
                if (json.success === false && json.message === "No se encontraron rutas") {
                    $('#routesTable tbody').html(
                        '<tr><td colspan="3" class="text-center">No se encontraron rutas</td></tr>'
                    );
                    return [];
                }
                // json.data ya es un array de rutas, cada una con un sub-array users
                return json.data || [];
            }
        },
        columns: [
            // 0. enumerador
            {
                data: null,
                title: '#',
                render: (data, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            // 1. Nombre de la ruta
            {
                data: 'nameRoute',
                title: 'Ruta'
            },
            // 2. Lista de usuarios (nombre, apellidos y rol)
            {
                data: 'users',
                title: 'Encargados',
                render: (users, type, row) => {
                    return users
                        .map(u => `${u.nombre} ${u.apellidos} (${u.role})`)
                        .join('<br>');
                }
            },
            {
                data: 'registerType',
                render: (type) => {
                    switch (type) {
                        case 1:
                            return 'Acceso peatonal';
                        case 2:
                            return 'Vehicular';
                        case 3:
                            return 'Autobús escolar';
                        default:
                            return 'Desconocido';
                    }
                }
            },
            // 3. Botones de acción
            {
                data: null,
                orderable: false,
                searchable: false,
                title: 'Acciones',
                render: (data, type, row) => `
                <div class="action-buttons">
                    <button 
                        type="button" 
                        class="action-btn edit-btn tooltip-btn" 
                        data-id="${row.idRoute}" 
                        data-tooltip="Editar ruta">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button 
                        type="button" 
                        class="action-btn delete-btn tooltip-btn" 
                        data-id="${row.idRoute}" 
                        data-tooltip="Eliminar ruta">
                        <i class="fas fa-trash-alt"></i>
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
            infoFiltered: "(filtrado de _MAX_ filas totales)",
            zeroRecords: "No se encontraron resultados"
        }
    });


    // Handler para el envío del formulario de nueva ruta
    $('#newRouteForm').on('submit', function (event) {
        event.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'newRoute' });
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
                    $('#newRouteForm')[0].reset();
                    $('#routeModal').modal('hide');
                }
            },
            error: error => console.error(error)
        });
    });

    // Handler para el envío del formulario de edición
    $('#editRouteForm').on('submit', function (event) {
        event.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'editRoute' });
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    $('#editRouteForm')[0].reset();
                    $('#editRouteModal').modal('hide');
                    table.ajax.reload();
                }
            },
            error: error => console.error(error)
        });
    });

    // Manejador para el botón editar (delegado)
    $('#routesTable').on('click', '.edit-btn', function () {
        const idRoute = $(this).data('id');
        $.ajax({
            url: 'controller/selectAction.php',
            method: 'POST',
            data: { action: 'getRoute', idRoute },
            dataType: 'json',
            success: response => {
                if (response.error) {
                    alert(response.error);
                } else {
                    const route = response.data;
                    $('#editRouteId').val(route.idRoute);
                    $('#editNombre').val(route.nameRoute);
                    $('#editTipoRegistro').val(route.registerType).trigger('change');

                    // Seleccionar automáticamente los encargados en el select2
                    const userIds = (route.users || []).map(u => u.userId);
                    $('#editEncargado').val(userIds).trigger('change');

                    $('#editRouteModal').modal('show');
                }
            },
            error: error => console.error(error)
        });
    });

    // Manejador para el botón eliminar (delegado)
    $('#routesTable').on('click', '.delete-btn', function () {
        const idRoute = $(this).data('id');
        if (confirm('¿Estás seguro de eliminar esta ruta?')) {
            $.ajax({
                url: 'controller/selectAction.php',
                method: 'POST',
                data: { action: 'deleteRoute', idRoute },
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