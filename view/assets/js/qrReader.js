// Variables globales para el escáner y ajustes de zoom
let html5QrcodeScanner;
let currentZoom = 1.0;
let videoTrack = null;          // Se usará para aplicar constraints de zoom
let failureCount = 0;           // Conteo de errores consecutivos (falta de QR)
let lastZoomDirection = 'in';   // Dirección del último ajuste automático
let autoFocusTimer = null;      // Temporizador para el autoajuste
let initialPinchDistance = null; // Para cálculo del pellizco (pinch-to-zoom)
let initialZoom = currentZoom;  // Zoom de partida en pellizco

// Función llamada cuando se detecta un QR (éxito en el escaneo)
function onScanSuccess(decodedText, decodedResult) {
    // Expresión regular para extraer la matrícula
    const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
    const match = decodedText.match(validQrRegex);

    if (match) {
        let matricula = match[1];
        $("#qr-result").html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`);
        console.log(`Matrícula detectada: ${matricula}`);

        // Simula detener el escaneo (busca el botón de "stop")
        let stopButton = $("#html5-qrcode-button-camera-stop");
        if (stopButton.length) {
            stopButton.click();
        }
        // Cuando se detecta el QR, se detiene el autoajuste
        if (autoFocusTimer) {
            clearInterval(autoFocusTimer);
            autoFocusTimer = null;
        }
    } else {
        $("#qr-result").html('<span style="color: red;">❌ El QR no es correcto</span>');
    }
    // Reiniciamos el contador de errores al lograr una lectura
    failureCount = 0;
}

// Función llamada cuando falla el escaneo
function onScanFailure(error) {
    // Si no se encontró ningún QR, se incrementa el contador
    if (error.indexOf("No QR code found") !== -1) {
        failureCount++;
        $("#qr-result").html('<span style="color: orange;">⚠️ Ajustando enfoque automáticamente...</span>');
    } else {
        $("#qr-result").html('<span style="color: orange;">⚠️ Acerca o aleja la cámara para enfocar mejor</span>');
    }
}

// Función para ajustar el zoom suavemente (manual o automático)
async function tryZoom(track, direction = 'in', step = 0.1) {
    try {
        const capabilities = track.getCapabilities();
        if (!capabilities.zoom) return; // Si el dispositivo no soporta zoom manual
        const min = capabilities.zoom.min || 1;
        const max = capabilities.zoom.max || 3; // Valor por defecto si no se define

        // Se ajusta el zoom según la dirección solicitada
        if (direction === 'in') {
            currentZoom = Math.min(currentZoom + step, max);
        } else {
            currentZoom = Math.max(currentZoom - step, min);
        }
        await track.applyConstraints({
            advanced: [{ zoom: currentZoom }]
        });
        console.log(`Zoom ${direction}: ${currentZoom.toFixed(2)}`);
    } catch (error) {
        console.log("Zoom no soportado o error al aplicar constraints:", error);
    }
}

// Función que se ejecuta periódicamente para ajustar automáticamente el zoom
async function autoAdjustFocus() {
    if (!videoTrack) return;
    // Si se han acumulado al menos 3 errores consecutivos, se realiza un ajuste
    if (failureCount >= 3) {
        const capabilities = videoTrack.getCapabilities();
        if (!capabilities.zoom) return;
        const min = capabilities.zoom.min || 1;
        const max = capabilities.zoom.max || 3;

        // Decide la dirección: si el zoom actual está muy cerca del mínimo, forzar acercar;
        // si está en el extremo, forzar alejar; de lo contrario, alterna la dirección.
        let direction = lastZoomDirection;
        if (currentZoom <= min + 0.1) {
            direction = 'in';
        } else if (currentZoom >= max - 0.1) {
            direction = 'out';
        }
        // Se aplica un ajuste de 0.2 (más pronunciado en el autoajuste)
        await tryZoom(videoTrack, direction, 0.2);
        // Se alterna la dirección para el siguiente ajuste
        lastZoomDirection = (direction === 'in' ? 'out' : 'in');
        // Reinicia el contador de errores
        failureCount = 0;
    }
}

// Función que inicializa el escáner
function initializeScanner() {
    // Configuración del escáner sin opción de seleccionar cámara
    const config = {
        fps: 10,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
            const minSize = Math.min(viewfinderWidth, viewfinderHeight);
            return { 
                width: minSize * 0.6, 
                height: minSize * 0.6 
            };
        },
        videoConstraints: {
            facingMode: "environment"  // Utiliza la cámara trasera
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
        showCameraPicker: false       // Deshabilita la opción de selección de cámara
    };

    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>');
    $("#qr-result").show();
    $("#reader").show();
    $("#start").hide();

    // Inicia el autoajuste cada 2 segundos
    autoFocusTimer = setInterval(autoAdjustFocus, 2000);
}

// Botón para iniciar el escaneo
$("#start").click(() => {
    if (!html5QrcodeScanner) {
        initializeScanner();
    } else {
        html5QrcodeScanner.clear();
        initializeScanner();
    }
});

// Variables para detectar toques dobles (double tap)
let lastTapTime = 0;
const DOUBLE_TAP_DELAY = 300;

$(document).ready(() => {
    // Observa cambios en el contenedor para capturar el elemento <video> cuando esté disponible
    const observer = new MutationObserver(() => {
        const videoElement = $("#reader video").get(0);
        if (videoElement) {
            // Guarda el track de video para aplicar constraints (zoom)
            if (videoElement.srcObject && videoElement.srcObject.getVideoTracks().length > 0) {
                videoTrack = videoElement.srcObject.getVideoTracks()[0];
            }

            // -----------------------------
            // CONTROLES TÁCTILES AVANZADOS
            // -----------------------------

            // Manejo de toque simple y doble toque para ajustar zoom
            $(videoElement).on("touchstart", async (e) => {
                if (e.touches.length === 1) {
                    e.preventDefault();
                    const currentTime = new Date().getTime();
                    const tapLength = currentTime - lastTapTime;
                    
                    if (tapLength < DOUBLE_TAP_DELAY && tapLength > 0) {
                        // Doble toque: se aleja el zoom
                        if (videoTrack) {
                            await tryZoom(videoTrack, 'out');
                            $("#qr-result").html('<span style="color: blue;">🔍 Alejando...</span>');
                        }
                    } else {
                        // Toque simple: se acerca el zoom
                        if (videoTrack) {
                            await tryZoom(videoTrack, 'in');
                            $("#qr-result").html('<span style="color: blue;">🔍 Acercando...</span>');
                        }
                    }
                    lastTapTime = currentTime;
                    
                    setTimeout(() => {
                        $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>');
                    }, 1000);
                }
            });

            // Soporte para pellizco (pinch-to-zoom)
            $(videoElement).on("touchmove", async (e) => {
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    const currentDistance = Math.hypot(
                        touch2.pageX - touch1.pageX,
                        touch2.pageY - touch1.pageY
                    );
                    if (initialPinchDistance === null) {
                        // Al iniciar el pellizco se guarda la distancia y el zoom actual
                        initialPinchDistance = currentDistance;
                        initialZoom = currentZoom;
                    } else {
                        // Se calcula la escala y se aplica el nuevo zoom
                        const scale = currentDistance / initialPinchDistance;
                        let newZoom = initialZoom * scale;
                        const capabilities = videoTrack ? videoTrack.getCapabilities() : { zoom: { min: 1, max: 3 } };
                        const min = capabilities.zoom.min || 1;
                        const max = capabilities.zoom.max || 3;
                        newZoom = Math.max(min, Math.min(max, newZoom));
                        currentZoom = newZoom;
                        if (videoTrack) {
                            await videoTrack.applyConstraints({
                                advanced: [{ zoom: currentZoom }]
                            });
                        }
                        $("#qr-result").html(`<span style="color: blue;">🔍 Ajustando zoom: ${currentZoom.toFixed(1)}</span>`);
                    }
                }
            });

            // Al finalizar el pellizco se reinician los valores
            $(videoElement).on("touchend", (e) => {
                if (e.touches.length < 2) {
                    initialPinchDistance = null;
                    setTimeout(() => {
                        $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>');
                    }, 1000);
                }
            });

            // Una vez configurados los eventos, se desconecta el observador
            observer.disconnect();
        }
    });
    observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
});
