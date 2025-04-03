<style>
</style>

<body>
    <!-- ENCABEZADO -->
    <header class="app-header">
        <img src="assets/images/logo.png" alt="Logo" width="150">
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="container">
        <div class="reader-button-wrapper" class="col-12">
        </div>
    </main>

    <!-- FOOTER NAVIGATION (solo en móviles) -->
    <footer class="app-footer">
        <nav class="footer-nav">
            <a href="qrScan" class="nav-item">
                <i class="fas fa-qrcode"></i>
                <span class="active-dot"></span> <!-- Punto indicador -->
            </a>
            <a href="routes" class="nav-item btn active">
                <i class="fal fa-exchange-alt"></i>
                <span class="active-dot"></span>
            </a>
            <a href="#" class="nav-item logout">
                <i class="fal fa-sign-out-alt"></i>
                <span class="active-dot"></span>
            </a>
        </nav>
    </footer>

    <!-- BOTÓN DE CERRAR SESIÓN -->
    <div class="logout-button-container">
        <a href="#" class="btn btn-danger logout-btn logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>
    <script>
        $(document).ready(function() {
            // Function to load routes and generate buttons
            function loadRoutes() {
                $.ajax({
                    url: 'controller/selectAction.php',
                    method: 'POST',
                    data: {
                        action: 'getRoutes' // Action to fetch routes
                    },
                    dataType: 'json',
                    success: function(response) {
                        const buttonContainer = $('.reader-button-wrapper');
                        buttonContainer.empty(); // Clear existing buttons

                        if (response.success && response.data.length > 0) {
                            // Generate button for each route
                            response.data.forEach(route => {
                                const button = $(`
                                    <button class="btn btn-primary route-button m-2" data-route-id="${route.idRoute}">
                                        <i class="fas fa-route me-2"></i>${route.nameRoute}
                                    </button>
                                `);

                                // Add click event to handle route selection
                                button.on('click', function() {
                                    const routeId = $(this).data('route-id');
                                    selectRoute(routeId);
                                });

                                buttonContainer.append(button);
                            });
                        } else {
                            buttonContainer.html('<div class="alert alert-info">No hay rutas disponibles</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.reader-button-wrapper').html(
                            '<div class="alert alert-danger">Error al cargar rutas: ' + error + '</div>'
                        );
                    }
                });
            }

            // Function to handle route selection
            function selectRoute(routeId) {
                $.ajax({
                    url: 'controller/selectAction.php',
                    method: 'POST',
                    data: {
                        action: 'selectRoute',
                        routeId: routeId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Highlight selected route
                            $('.route-button').removeClass('active');
                            $(`.route-button[data-route-id="${routeId}"]`).addClass('active');

                            // Optional: show success message
                            showToast('Ruta seleccionada correctamente', 'success');
                            window.location.href = 'qrScan'; // Redirect to QR scan page
                        } else {
                            showToast(response.message || 'Error al seleccionar ruta', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error de conexión', 'error');
                    }
                });
            }

            // Simple toast notification function
            function showToast(message, type = 'info') {
                const toast = $(`<div class="toast-notification toast-${type}">${message}</div>`);
                $('body').append(toast);
                setTimeout(() => toast.addClass('show'), 100);
                setTimeout(() => {
                    toast.removeClass('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Initialize - load routes on page load
            loadRoutes();
        });
    </script>