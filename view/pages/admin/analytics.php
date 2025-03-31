<div class="content">
    <div class="container-fluid py-4">
        <!-- Dashboard Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="display-4 fw-bold mb-2">QR Analytics Dashboard</h1>
                        <p class="lead opacity-8">Bienvenido al panel de análisis. Aquí puedes monitorear el uso del sistema de escaneo de QR.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="fas fa-qrcode text-primary fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Total de Escaneos</p>
                                <h3 class="fw-bold mb-0" id="total-scans">Cargando...</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="fas fa-users text-success fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Usuarios Activos</p>
                                <h3 class="fw-bold mb-0" id="active-users">Cargando...</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="fas fa-clock text-info fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Último Escaneo</p>
                                <h3 class="fw-bold mb-0" id="last-scan">Cargando...</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Estadísticas de Escaneos</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="scan-trends-chart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Distribución</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <canvas id="distribution-chart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Bitácora de Escaneos</h5>
                        <button class="btn btn-primary btn-sm" onclick="loadScanLogs()">
                            <i class="fas fa-sync-alt me-2"></i>Actualizar
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="log-container" class="table-responsive">
                            <table id="scan-logs-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Ruta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center">Cargando datos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar datos
            document.getElementById('total-scans').textContent = '3,456';
            document.getElementById('active-users').textContent = '25';
            document.getElementById('last-scan').textContent = 'Hoy, 14:35';
            
            // Inicializar DataTable
            // $('#scan-logs-table').DataTable({
            //     language: {
            //         url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            //     },
            //     responsive: true,
            //     pageLength: 10
            // });

            // Inicializar gráfico de tendencias
            const trendCtx = document.getElementById('scan-trends-chart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                    datasets: [{
                        label: 'Escaneos por Día',
                        data: [50, 75, 60, 90, 120, 85, 40],
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: 'rgba(54, 162, 235, 1)',
                        pointBorderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Inicializar gráfico de distribución
            const distCtx = document.getElementById('distribution-chart').getContext('2d');
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Aulas', 'Biblioteca', 'Cafetería', 'Laboratorios', 'Otros'],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        });

        function loadScanLogs() {
            // Simulación de carga de datos
            $('#scan-logs-table').DataTable().clear().destroy();
            
            // Datos de ejemplo que serían reemplazados por datos reales de la BD
            const logData = [
                ['2023-10-01 14:35', 'Juan Pérez', 'Aula 101'],
                ['2023-10-01 14:40', 'María López', 'Biblioteca'],
                ['2023-10-01 15:10', 'Carlos Ruiz', 'Laboratorio 3'],
                ['2023-10-01 15:45', 'Ana García', 'Cafetería'],
                ['2023-10-02 09:20', 'Pedro Sánchez', 'Aula 205'],
                ['2023-10-02 10:15', 'Laura Martínez', 'Biblioteca'],
                ['2023-10-02 11:30', 'Miguel Torres', 'Aula 101']
            ];
            
            // Inicializar DataTable con los nuevos datos
            $('#scan-logs-table').DataTable({
                data: logData,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                responsive: true,
                pageLength: 10
            });
            
            // Mostrar notificación
            toastr.success('Datos de bitácora actualizados correctamente', 'Éxito');
        }
    </script>
</div>