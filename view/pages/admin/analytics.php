<div class="content">
    <div class="container-fluid py-4">
        <!-- Dashboard Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="display-4 fw-bold mb-2">QR Analytics Dashboard</h1>
                        <p class="lead opacity-8">Bienvenido al panel de análisis. Aquí puedes monitorear el uso del
                            sistema de escaneo de QR.</p>
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
                    <div class="card-footer">
                        <span id="card-footer-text" style="font-weight: bold;"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Table Card -->
        <div class="row mb-5">
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

    <script src="assets/js/chart.js"></script>
    <script>
        $(document).ready(function () {

            // Inicializar datos
            $('#total-scans').text('3,456');
            $('#active-users').text('25');
            $('#last-scan').text('Hoy, 14:35');

            // Inicializar gráfico de tendencias
            const trendCtx = $('#scan-trends-chart')[0].getContext('2d');
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

            function getRandomColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            function getRandolValue() {
                return Math.floor(Math.random() * 10+1);
            }

            const camiones = [
                { label: 'Camión 1', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' },
                { label: 'Camión 2', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' },
                { label: 'Camión 3', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' },
                { label: 'Camión 4', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' },
                { label: 'Camión 5', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' },
                { label: 'Camión 6', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' },
                { label: 'Camión 7', valor: getRandolValue(), color: getRandomColor(), cutout: '80%', radius: '100%' }
            ];

            const total = camiones.reduce((sum, c) => sum + c.valor, 0);

            const datasets = camiones.map(c => ({
                label: c.label,
                data: [c.valor],
                backgroundColor: [c.color],
                borderWidth: 2,
                borderColor: '#ffffff',
                borderRadius: 10,
                cutout: c.cutout,
                radius: c.radius,
                circumference: (360 * c.valor) / total,
                rotation: -90
            }));

            // Variable global para el texto central
            let centerText = { label: '', value: '' };

            // Plugin para mostrar texto en el centro
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw(chart) {
                    const { ctx, chartArea: { width, height } } = chart;
                    ctx.save();
                    ctx.font = 'bold 18px Segoe UI';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    if (centerText.label) {
                        ctx.fillText(centerText.label, width / 2, height / 2 - 10);
                        ctx.font = 'bold 20px Segoe UI';
                        ctx.fillText(centerText.value, width / 2, height / 2 + 15);
                    }
                    ctx.restore();
                }
            };

            const distCtx = document.getElementById('distribution-chart').getContext('2d');

            const chart = new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, elements) => {
                        // Cambia el cursor a pointer si se está pasando sobre algún elemento
                        event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                    },
                    onClick(evt, elements) {
                        if (elements.length > 0) {
                            const index = elements[0].datasetIndex;
                            const dataset = chart.data.datasets[index];

                            // En lugar de cambiar texto en el centro, lo imprimimos en el footer
                            const footerElement = document.getElementById('card-footer-text');
                            footerElement.textContent = `${dataset.label} - Cantidad: ${dataset.data[0]}`;
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: ctx => ctx[0].dataset.label,
                                label: ctx => `Cantidad: ${ctx.dataset.data[0]}`
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function (chart) {
                                    return chart.data.datasets.map((dataset, i) => ({
                                        text: dataset.label,
                                        fillStyle: dataset.backgroundColor[0],
                                        strokeStyle: dataset.backgroundColor[0],
                                        index: i
                                    }));
                                },
                                color: '#333',
                                font: {
                                    size: 14
                                },
                                boxWidth: 14,
                                usePointStyle: true
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });
        });


    </script>
</div>