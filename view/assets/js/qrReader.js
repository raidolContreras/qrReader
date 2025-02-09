let html5QrcodeScanner;

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
    $("#qr-result").html('<span style="color: orange;">⚠️ Error de escaneo. Ajusta la iluminación o enfoca mejor.</span>');
}

// Función mejorada para activar el enfoque automático
async function triggerAutofocus(videoElement) {
    if (!videoElement || !videoElement.srcObject) {
        console.error("No se encontró el elemento de video o stream");
        return;
    }

    try {
        const track = videoElement.srcObject.getVideoTracks()[0];
        const capabilities = track.getCapabilities();

        // Verificar las capacidades de enfoque disponibles
        console.log("Capacidades de la cámara:", capabilities);

        // Intentar múltiples métodos de enfoque
        const focusModes = ["continuous", "auto", "manual"];
        
        for (const mode of focusModes) {
            try {
                // Primero intentamos desactivar el enfoque actual
                await track.applyConstraints({
                    advanced: [{ focusMode: "none" }]
                });

                // Luego aplicamos el nuevo modo de enfoque
                await track.applyConstraints({
                    advanced: [{
                        focusMode: mode
                    }]
                });

                // Si llegamos aquí, el modo de enfoque se aplicó correctamente
                console.log(`Modo de enfoque aplicado: ${mode}`);
                $("#qr-result").html(`<span style="color: blue;">🔍 Ajustando enfoque (${mode})...</span>`);
                
                // Para modo manual, intentamos establecer un punto de enfoque central
                if (mode === "manual" && capabilities.focusDistance) {
                    await track.applyConstraints({
                        advanced: [{
                            focusMode: "manual",
                            focusDistance: 0.5 // Valor medio para enfoque
                        }]
                    });
                }

                break; // Si un modo funciona, salimos del bucle
            } catch (error) {
                console.warn(`Error al aplicar modo de enfoque ${mode}:`, error);
                continue; // Intentar con el siguiente modo
            }
        }

        // Intentar ajustar otros parámetros que pueden ayudar al enfoque
        const additionalConstraints = {
            advanced: [{
                zoom: 1.0,
                brightness: 1.0,
                sharpness: 1.0
            }]
        };

        await track.applyConstraints(additionalConstraints).catch(console.error);

        // Restaurar mensaje normal después de un tiempo
        setTimeout(() => {
            $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Enfoca el código QR.</span>');
        }, 2000);

    } catch (error) {
        console.error("Error general al ajustar el enfoque:", error);
        $("#qr-result").html('<span style="color: orange;">⚠️ Error al enfocar. Intenta de nuevo.</span>');
    }
}

function initializeScanner() {
    const config = {
        fps: 30,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
            const minSize = Math.min(viewfinderWidth, viewfinderHeight);
            return { width: minSize * 0.8, height: minSize * 0.8 };
        },
        videoConstraints: {
            facingMode: "environment",
            width: { ideal: 1920 },
            height: { ideal: 1080 },
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

    $("#qr-result").html('<span style="color: blue;">🔍 Escaneando... Enfoca el código QR.</span>');
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

// Mejorado el manejo de eventos táctiles
$(document).ready(() => {
    const observer = new MutationObserver(() => {
        const videoElement = $("#reader video").get(0);
        if (videoElement) {
            // Agregar tanto click como touch events
            $(videoElement).on("click touchstart", (e) => {
                e.preventDefault(); // Prevenir comportamiento por defecto
                triggerAutofocus(videoElement);
            });
            
            // Logging inicial de capacidades
            if (videoElement.srcObject) {
                const track = videoElement.srcObject.getVideoTracks()[0];
                console.log("Capacidades iniciales de la cámara:", track.getCapabilities());
            }
            
            observer.disconnect();
        }
    });
    observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
});