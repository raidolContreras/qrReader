let html5QrcodeScanner;

function onScanSuccess(decodedText, decodedResult) {
    // Expresión regular para extraer la matrícula
    const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
    const match = decodedText.match(validQrRegex);

    if (match) {
        let matricula = match[1]; // Extrae solo el número de matrícula
        // ✅ QR válido
        $("#qr-result").html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`);
        console.log(`Matrícula detectada: ${matricula}`);

        // Simular clic en el botón "Detener escaneo" (sin detener el escáner completamente)
        let stopButton = $("#html5-qrcode-button-camera-stop");
        if (stopButton.length) {
            stopButton.click();
        } else {
            console.warn("El botón 'Stop Scanning' no se encontró.");
        }
    } else {
        // ❌ QR inválido: continuar escaneando
        $("#qr-result").html('<span style="color: red;">❌ El QR no es correcto</span>');
        console.warn("QR inválido detectado:", decodedText);
    }
}

function onScanFailure(error) {
    // Mostrar mensaje de error al usuario
    $("#qr-result").html('<span style="color: orange;">⚠️ Error de escaneo. Ajusta la iluminación o enfoca mejor.</span>');
    // console.warn(`Error de escaneo: ${error}`);
}

function initializeScanner() {
    // Configuración avanzada
    const config = {
        fps: 30, // Tasa de fotogramas por segundo
        qrbox: (viewfinderWidth, viewfinderHeight) => {
            const minSize = Math.min(viewfinderWidth, viewfinderHeight);
            return { width: minSize * 0.8, height: minSize * 0.8 }; // Tamaño del área de escaneo
        },
        videoConstraints: {
            facingMode: "environment", // Usar la cámara trasera
            advanced: [
                { focusMode: "continuous" }, // Enfoque continuo
                { exposureMode: "continuous" }, // Exposición continua
                { whiteBalanceMode: "continuous" } // Balance de blancos continuo
            ],
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA], // Solo escaneo por cámara
    };

    // Inicializa el escáner con la configuración
    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    // Mostrar mensaje inicial
    $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Enfoca el código QR.</span>');
    $("#qr-result").show(); // Mostrar el área de resultados
    $("#reader").show(); // Mostrar el escáner
    $("#start").hide(); 
}

// Botón para iniciar escaneo
$("#start").click(async () => {
    if (!html5QrcodeScanner) {
        initializeScanner(); // Inicializa el escáner si no está inicializado

    } else {
        // Reiniciar el escáner si ya está inicializado
        html5QrcodeScanner.clear(); // Limpiar el escáner actual
        initializeScanner(); // Volver a inicializar
    }
});

// Habilitar tap-to-focus manual (opcional)
function enableTapToFocus(videoElement) {
    $(videoElement).on("click", async (event) => {
        if (videoElement && videoElement.srcObject) {
            const track = videoElement.srcObject.getVideoTracks()[0];
            const capabilities = track.getCapabilities();
            // Verifica si el enfoque manual es soportado
            if (capabilities.focusMode && capabilities.focusMode.includes("manual")) {
                try {
                    const rect = videoElement.getBoundingClientRect();
                    const x = (event.clientX - rect.left) / rect.width;
                    const y = (event.clientY - rect.top) / rect.height;
                    // Configura el punto de enfoque
                    await track.applyConstraints({
                        advanced: [
                            {
                                focusMode: "manual",
                                focusPointX: x,
                                focusPointY: y,
                            },
                        ],
                    });
                    $("#qr-result").html('<span style="color: blue;">👆 Enfocando...</span>');
                    console.log(`Autoenfoque aplicado en: (${x}, ${y})`);
                } catch (error) {
                    console.error("Error al aplicar el autoenfoque:", error);
                    $("#qr-result").html('<span style="color: orange;">⚠️ Error al enfocar. Intenta de nuevo.</span>');
                }
            } else {
                console.warn("El enfoque manual no es compatible con esta cámara.");
                $("#qr-result").html('<span style="color: orange;">⚠️ Enfoque manual no disponible.</span>');
            }
        }
    });
}

$(document).ready(() => {
    // Habilitar tap-to-focus (opcional)
    const observer = new MutationObserver(() => {
        const videoElement = $("#reader video").get(0);
        if (videoElement) {
            enableTapToFocus(videoElement);
            observer.disconnect();
        }
    });
    observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
});