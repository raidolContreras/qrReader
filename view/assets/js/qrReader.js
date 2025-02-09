let html5QrcodeScanner;

    function onScanSuccess(decodedText, decodedResult) {
      // Expresión regular para extraer la matrícula
      const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
      const match = decodedText.match(validQrRegex);

      if (match) {
        let matricula = match[1];
        $("#qr-result").html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`);
        console.log(`Matrícula detectada: ${matricula}`);

        // Simula el clic para detener el escaneo
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

    function initializeScanner() {
      const config = {
        fps: 30,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
          const minSize = Math.min(viewfinderWidth, viewfinderHeight);
          return { width: minSize * 0.8, height: minSize * 0.8 };
        },
        videoConstraints: {
          facingMode: "environment",
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

    // Función para habilitar tap-to-focus (opcional)
    function enableTapToFocus(videoElement) {
      $(videoElement).on("click", async (event) => {
        if (videoElement && videoElement.srcObject) {
          const track = videoElement.srcObject.getVideoTracks()[0];
          const capabilities = track.getCapabilities();
          if (capabilities.focusMode && capabilities.focusMode.includes("manual")) {
            try {
              const rect = videoElement.getBoundingClientRect();
              const x = (event.clientX - rect.left) / rect.width;
              const y = (event.clientY - rect.top) / rect.height;
              await track.applyConstraints({
                advanced: [
                  { focusMode: "manual", focusPointX: x, focusPointY: y }
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
      const observer = new MutationObserver(() => {
        const videoElement = $("#reader video").get(0);
        if (videoElement) {
          enableTapToFocus(videoElement);
          observer.disconnect();
        }
      });
      observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
    });