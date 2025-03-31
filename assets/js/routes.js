
$(document).ready(() => {
    // Instancia de DataTable
    const table = $('#routesTable').DataTable({
        ajax: {
            url: 'controller/selectAction.php',
            type: 'POST',
            data: { action: 'getRoutes' },
            dataType: 'json',
            dataSrc: 'data' // El array está en la raíz del JSON
        },
        columns: [
            { data: 'nameRoute' },
            {
                data: null,
                render: (data, type, row) => `
                <div class="action-buttons">
                    <button type="button" class="action-btn edit-btn tooltip-btn" data-id="${row.idRoute}" data-tooltip="Editar ruta">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button type="button" class="action-btn delete-btn tooltip-btn" data-id="${row.idRoute}" data-tooltip="Eliminar ruta">
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