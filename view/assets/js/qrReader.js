// === Aquí va tu código JS original (con las funciones de escaneo y zoom) ===
// === Copia y pega directamente tu contenido de qrReader.js o el script que compartiste. ===

// Variables globales para el escáner y ajustes de zoom
let html5QrcodeScanner;
let currentZoom = 1.0;
let videoTrack = null;
let failureCount = 0;
let lastZoomDirection = 'in';
let autoFocusTimer = null;
let initialPinchDistance = null;
let initialZoom = currentZoom;

function onScanSuccess(decodedText, decodedResult) {
    const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
    const match = decodedText.match(validQrRegex);

    if (match) {
        let matricula = match[1];
        $("#qr-result").html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`).show();
        console.log(`Matrícula detectada: ${matricula}`);

        // Detenemos el escáner
        let stopButton = $("#html5-qrcode-button-camera-stop");
        if (stopButton.length) {
            stopButton.click();
        }
        if (autoFocusTimer) {
            clearInterval(autoFocusTimer);
            autoFocusTimer = null;
        }
    } else {
        $("#qr-result").html('<span style="color: red;">❌ El QR no es correcto</span>').show();
    }
    failureCount = 0;
}

function onScanFailure(error) {
    failureCount++;
    if (error.indexOf("No QR code found") !== -1) {
        $("#qr-result").html(
            '<span style="color: orange;">⚠️ Ajustando enfoque automáticamente...</span>'
        ).show();
    } else {
        $("#qr-result").html(
            '<span style="color: orange;">⚠️ Acerca o aleja la cámara para enfocar mejor</span>'
        ).show();
    }
}

async function tryZoom(track, direction = 'in', step = 0.1) {
    try {
        const capabilities = track.getCapabilities();
        if (!capabilities.zoom) return;
        const min = capabilities.zoom.min || 1;
        const max = capabilities.zoom.max || 3;

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

async function autoAdjustFocus() {
    if (!videoTrack) return;
    if (failureCount >= 3) {
        const capabilities = videoTrack.getCapabilities();
        if (!capabilities.zoom) return;
        const min = capabilities.zoom.min || 1;
        const max = capabilities.zoom.max || 3;
        let direction = lastZoomDirection;
        if (currentZoom <= min + 0.1) {
            direction = 'in';
        } else if (currentZoom >= max - 0.1) {
            direction = 'out';
        }
        await tryZoom(videoTrack, direction, 0.2);
        lastZoomDirection = (direction === 'in' ? 'out' : 'in');
        failureCount = 0;
    }
}

function initializeScanner() {
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
            facingMode: "environment"
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
        showCameraPicker: false
    };

    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    $("#qr-result").html(
        '<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>'
    ).show();
    $("#reader").show();
    $("#start").hide();

    autoFocusTimer = setInterval(autoAdjustFocus, 2000);
}

$("#start").click(() => {
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
            if (videoElement.srcObject && videoElement.srcObject.getVideoTracks().length > 0) {
                videoTrack = videoElement.srcObject.getVideoTracks()[0];
            }
            $(videoElement).on("touchstart", async (e) => {
                if (e.touches.length === 1) {
                    e.preventDefault();
                    const currentTime = new Date().getTime();
                    const tapLength = currentTime - lastTapTime;

                    if (tapLength < DOUBLE_TAP_DELAY && tapLength > 0) {
                        if (videoTrack) {
                            await tryZoom(videoTrack, 'out');
                            $("#qr-result").html('<span style="color: blue;">🔍 Alejando...</span>');
                        }
                    } else {
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
            // Pellizco para zoom
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
                        initialPinchDistance = currentDistance;
                        initialZoom = currentZoom;
                    } else {
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
            $(videoElement).on("touchend", (e) => {
                if (e.touches.length < 2) {
                    initialPinchDistance = null;
                    setTimeout(() => {
                        $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>');
                    }, 1000);
                }
            });
            observer.disconnect();
        }
    });
    observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
});