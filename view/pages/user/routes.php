<style>
    .logout-button-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 100;
    }

    .logout-btn {
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 768px) {
        .logout-button-container {
            display: none;
            /* Hide on mobile since you have it in the footer nav */
        }
    }

    .btn {
        border: 0;
    }

    .route-button {
        transition: all 0.3s ease;
        border-radius: 15px;
        font-weight: 500;
        min-height: 60px;
    }

    .route-button.active {
        background-color: #28a745;
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .toast-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1000;
    }

    .toast-notification.show {
        opacity: 1;
    }

    .toast-success {
        background-color: #28a745;
    }

    .toast-error {
        background-color: #dc3545;
    }

    .toast-info {
        background-color: #17a2b8;
    }

    .reader-button-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 20px 0;
        align-items: stretch;
    }
</style>

<body>
    <!-- ENCABEZADO -->
    <header class="app-header">
        <img src="assets/images/logo-color.png" alt="Logo" width="150">
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="container row">
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