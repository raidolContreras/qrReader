let html5QrcodeScanner;
let currentZoom = 1.0;
let isScanning = false;

function onScanSuccess(decodedText, decodedResult) {
    const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
    const match = decodedText.match(validQrRegex);

    if (match) {
        let matricula = match[1];
        $("#qr-result").html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`);
        console.log(`Matrícula detectada: ${matricula}`);

        if (isScanning) {
            html5QrcodeScanner.pause();
            isScanning = false;
        }
    } else {
        $("#qr-result").html('<span style="color: red;">❌ El QR no es correcto</span>');
    }
}

function onScanFailure(error) {
    if (error !== "QR code parse error, error = No QR code found") {
        $("#qr-result").html('<span style="color: orange;">⚠️ Acerca o aleja la cámara para enfocar mejor</span>');
    }
}

async function tryZoom(track, direction = 'in') {
    try {
        const capabilities = track.getCapabilities();
        if (!capabilities.zoom) return;

        const settings = track.getSettings();
        const min = capabilities.zoom.min || 1;
        const max = capabilities.zoom.max || 2.5;

        if (direction === 'in') {
            currentZoom = Math.min(currentZoom + 0.1, max);
        } else {
            currentZoom = Math.max(currentZoom - 0.1, min);
        }

        await track.applyConstraints({
            advanced: [{ zoom: currentZoom }]
        });
    } catch (error) {
        console.log("Zoom no soportado en este dispositivo");
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
            facingMode: "environment",
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
    };

    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    isScanning = true;

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
            $(videoElement).on("touchstart", async (e) => {
                e.preventDefault();
                
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTapTime;
                
                if (tapLength < DOUBLE_TAP_DELAY && tapLength > 0) {
                    if (videoElement.srcObject) {
                        const track = videoElement.srcObject.getVideoTracks()[0];
                        await tryZoom(track, 'out');
                        $("#qr-result").html('<span style="color: blue;">🔍 Alejando...</span>');
                    }
                } else {
                    if (videoElement.srcObject) {
                        const track = videoElement.srcObject.getVideoTracks()[0];
                        await tryZoom(track, 'in');
                        $("#qr-result").html('<span style="color: blue;">🔍 Acercando...</span>');
                    }
                }
                
                lastTapTime = currentTime;

                setTimeout(() => {
                    $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Toca para ajustar zoom</span>');
                }, 1000);
            });
            
            observer.disconnect();
        }
    });
    observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
});