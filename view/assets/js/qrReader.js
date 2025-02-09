let html5QrcodeScanner;
let currentZoom = 1.0;
let selectedCameraId = null;

// Función para obtener y mostrar las cámaras disponibles
async function loadCameras() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        // Crear selector de cámaras
        const cameraSelect = $('<select>', {
            id: 'camera-select',
            class: 'form-control mb-2'
        });
        
        videoDevices.forEach(device => {
            cameraSelect.append($('<option>', {
                value: device.deviceId,
                text: device.label || `Cámara ${device.deviceId.slice(0, 5)}...`
            }));
        });
        
        // Agregar el selector al DOM
        $('#camera-controls').append(cameraSelect);
        
        // Evento de cambio de cámara
        cameraSelect.on('change', function() {
            selectedCameraId = $(this).val();
            restartScanner();
        });
        
        return videoDevices.length > 0 ? videoDevices[0].deviceId : null;
    } catch (error) {
        console.error("Error al cargar cámaras:", error);
        return null;
    }
}

// Función para crear controles de cámara
function createCameraControls() {
    // Primero creamos el contenedor principal
    const mainContainer = `
        <div class="scanner-container">
            <div id="camera-controls" class="mb-3">
                <h5>Controles de Cámara</h5>
                <div class="control-group mb-2">
                    <label>Zoom: <span id="zoom-value">1.0x</span></label>
                    <input type="range" id="zoom-control" min="10" max="40" value="10">
                </div>
                <div class="control-group mb-2">
                    <button id="torch-toggle" class="btn btn-secondary btn-sm">Activar Linterna</button>
                    <button id="focus-toggle" class="btn btn-secondary btn-sm">Bloquear Enfoque</button>
                </div>
            </div>
            <div id="reader"></div>
            <div id="qr-result" class="mt-3"></div>
        </div>
    `;
    
    // Reemplazar el contenido existente
    $('#reader').replaceWith(mainContainer);
    
    // Aplicar estilos necesarios
    const styles = `
        <style>
            .scanner-container {
                width: 100%;
                max-width: 640px;
                margin: 0 auto;
            }
            #camera-controls {
                padding: 10px;
                background: #f8f9fa;
                border-radius: 5px;
                margin-bottom: 15px;
            }
            .control-group {
                margin-bottom: 10px;
            }
            .control-group label {
                display: block;
                margin-bottom: 5px;
            }
            input[type="range"] {
                width: 100%;
            }
            .btn-sm {
                margin-right: 5px;
            }
            #reader {
                width: 100% !important;
                min-height: 300px !important;
                border: 2px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
            }
            #reader video {
                width: 100% !important;
                height: auto !important;
            }
            #qr-result {
                text-align: center;
                padding: 10px;
                font-size: 16px;
            }
            /* Asegurar que los elementos del escáner sean visibles */
            #reader__scan_region {
                width: 100% !important;
                height: 300px !important;
            }
            #reader__dashboard {
                margin-top: 10px;
            }
        </style>
    `;
    $('head').append(styles);
}

function initializeScanner() {
    const config = {
        fps: 10,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
            const minSize = Math.min(viewfinderWidth, viewfinderHeight);
            return { 
                width: minSize * 0.7, 
                height: minSize * 0.7 
            };
        },
        videoConstraints: {
            deviceId: selectedCameraId ? { exact: selectedCameraId } : undefined,
            facingMode: selectedCameraId ? undefined : "environment",
        },
        aspectRatio: 1.0
    };

    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    // Inicializar controles después de que la cámara esté lista
    setTimeout(initializeCameraControls, 1000);
}

async function initializeCameraControls() {
    const videoElement = $("#reader video").get(0);
    if (!videoElement || !videoElement.srcObject) return;

    const track = videoElement.srcObject.getVideoTracks()[0];
    const capabilities = track.getCapabilities();

    // Zoom control
    $('#zoom-control').on('input', async function() {
        const zoomValue = $(this).val() / 10;
        $('#zoom-value').text(zoomValue.toFixed(1) + 'x');
        try {
            await track.applyConstraints({
                advanced: [{ zoom: zoomValue }]
            });
        } catch (error) {
            console.log("Zoom no soportado");
        }
    });

    // Torch toggle
    $('#torch-toggle').on('click', async function() {
        const isActive = $(this).hasClass('active');
        try {
            await track.applyConstraints({
                advanced: [{ torch: !isActive }]
            });
            $(this).toggleClass('active')
                  .text(isActive ? 'Activar Linterna' : 'Desactivar Linterna');
        } catch (error) {
            console.log("Linterna no soportada");
            $(this).hide();
        }
    });

    // Focus toggle
    $('#focus-toggle').on('click', async function() {
        const isLocked = $(this).hasClass('active');
        try {
            await track.applyConstraints({
                advanced: [{ focusMode: isLocked ? 'continuous' : 'manual' }]
            });
            $(this).toggleClass('active')
                  .text(isLocked ? 'Bloquear Enfoque' : 'Desbloquear Enfoque');
        } catch (error) {
            console.log("Control de enfoque no soportado");
            $(this).hide();
        }
    });

    // Ocultar controles no soportados
    if (!capabilities.zoom) $('#zoom-control').parent().hide();
    if (!capabilities.torch) $('#torch-toggle').hide();
    if (!capabilities.focusMode) $('#focus-toggle').hide();
}

function onScanSuccess(decodedText, decodedResult) {
    const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
    const match = decodedText.match(validQrRegex);

    if (match) {
        let matricula = match[1];
        $("#qr-result").html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`);
        console.log(`Matrícula detectada: ${matricula}`);

        let stopButton = $("#html5-qrcode-button-camera-stop");
        if (stopButton.length) {
            stopButton.click();
        }
    } else {
        $("#qr-result").html('<span style="color: red;">❌ El QR no es correcto</span>');
    }
}

function onScanFailure(error) {
    if (error !== "QR code parse error, error = No QR code found") {
        $("#qr-result").html('<span style="color: orange;">⚠️ Ajusta los controles para mejorar la lectura</span>');
    }
}

function restartScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
    initializeScanner();
}

// Inicialización
$(document).ready(async () => {
    createCameraControls();
    selectedCameraId = await loadCameras();
    initializeScanner();
});

// Botón de inicio
$("#start").click(async () => {
    restartScanner();
});