let html5QrcodeScanner;
let currentZoom = 1.0;
const MAX_ZOOM = 2.5; // Zoom máximo permitido

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
        } else {
            console.warn("El botón 'Stop Scanning' no se encontró.");
        }
    } else {
        $("#qr-result").html('<span style="color: red;">❌ El QR no es correcto</span>');
        console.warn("QR inválido detectado:", decodedText);
    }
}

function onScanFailure(error) {
    // Solo mostrar errores críticos, no los de escaneo normal
    if (error !== "QR code parse error, error = No QR code found") {
        $("#qr-result").html('<span style="color: orange;">⚠️ Acerca o aleja la cámara para enfocar mejor</span>');
    }
}

async function adjustZoom(track, direction = 'in') {
    try {
        const capabilities = track.getCapabilities();
        if (!capabilities.zoom) return;

        const settings = track.getSettings();
        const min = capabilities.zoom.min || 1;
        const max = Math.min(capabilities.zoom.max || 2.5, MAX_ZOOM);
        const step = (max - min) / 10;

        if (direction === 'in' && currentZoom < max) {
            currentZoom = Math.min(currentZoom + step, max);
        } else if (direction === 'out' && currentZoom > min) {
            currentZoom = Math.max(currentZoom - step, min);
        }

        await track.applyConstraints({
            advanced: [{ zoom: currentZoom }]
        });

        console.log(`Zoom ajustado a: ${currentZoom}`);
    } catch (error) {
        console.error("Error al ajustar zoom:", error);
    }
}

async function optimizeCameraSettings(videoElement) {
    if (!videoElement || !videoElement.srcObject) return;

    try {
        const track = videoElement.srcObject.getVideoTracks()[0];
        const capabilities = track.getCapabilities();

        // Aplicar configuraciones óptimas para móviles
        const constraints = {
            advanced: [{
                // Priorizar la nitidez sobre la velocidad
                focusMode: "continuous",
                exposureMode: "continuous",
                whiteBalanceMode: "continuous",
                // Intentar establecer zoom inicial
                zoom: 1.2
            }]
        };

        if (capabilities.torch) {
            constraints.advanced[0].torch = true;
        }

        await track.applyConstraints(constraints);
        
        // Ajustar el zoom inicial
        await adjustZoom(track, 'in');

    } catch (error) {
        console.error("Error al optimizar la cámara:", error);
    }
}

function initializeScanner() {
    const config = {
        fps: 10, // Reducir FPS para mejor procesamiento
        qrbox: (viewfinderWidth, viewfinderHeight) => {
            const minSize = Math.min(viewfinderWidth, viewfinderHeight);
            // Área de escaneo más pequeña para mejor enfoque
            return { 
                width: minSize * 0.6, 
                height: minSize * 0.6 
            };
        },
        videoConstraints: {
            facingMode: "environment",
            width: { ideal: 1280 }, // Resolución óptima para móviles
            height: { ideal: 720 },
            advanced: [
                { focusMode: "continuous" },
                { exposureMode: "continuous" },
                { whiteBalanceMode: "continuous" }
            ],
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
    };

    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca la pantalla para ajustar zoom</span>');
    $("#qr-result").show();
    $("#reader").show();
    $("#start").hide();
}

$("#start").click(async () => {
    if (!html5QrcodeScanner) {
        initializeScanner();
    } else {
        html5QrcodeScanner.clear();
        initializeScanner();
    }
});

let lastTapTime = 0;
const DOUBLE_TAP_DELAY = 300;

$(document).ready(() => {
    const observer = new MutationObserver(() => {
        const videoElement = $("#reader video").get(0);
        if (videoElement) {
            // Optimizar configuración inicial
            optimizeCameraSettings(videoElement);

            // Manejar toques en la pantalla
            $(videoElement).on("touchstart", async (e) => {
                e.preventDefault();
                
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTapTime;
                
                if (tapLength < DOUBLE_TAP_DELAY && tapLength > 0) {
                    // Doble toque - reducir zoom
                    const track = videoElement.srcObject.getVideoTracks()[0];
                    await adjustZoom(track, 'out');
                    $("#qr-result").html('<span style="color: blue;">🔍 Alejando...</span>');
                } else {
                    // Toque simple - aumentar zoom
                    const track = videoElement.srcObject.getVideoTracks()[0];
                    await adjustZoom(track, 'in');
                    $("#qr-result").html('<span style="color: blue;">🔍 Acercando...</span>');
                }
                
                lastTapTime = currentTime;

                // Restaurar mensaje después de un momento
                setTimeout(() => {
                    $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca para ajustar zoom</span>');
                }, 1000);
            });
            
            observer.disconnect();
        }
    });
    observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
});