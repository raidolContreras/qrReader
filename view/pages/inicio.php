<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escaneo QR</title>
  <!-- Bootstrap 5 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      background-color: #f9f9f9;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }
    #reader {
      width: 100%;
      max-width: 600px;
      aspect-ratio: 4/3;
      margin-top: 20px;
      border: 2px solid #01643d !important;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
      display: flex;
      flex-direction: column;
      align-items: center; /* Centra horizontalmente */
      justify-content: center; /* Centra verticalmente */
      text-align: center;      /* Asegura que el texto e imágenes estén alineados */
      position: relative;      /* Mantiene los elementos posicionados correctamente */
    }
    #qr-result {
      margin-top: 20px;
      font-size: 18px;
      color: #01643d;
      text-align: center;
    }
    #restart-scan {
      display: none;
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 16px;
      color: #fff;
      background-color: #01643d;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    #restart-scan:hover {
      background-color: #014e2c;
    }
    @keyframes floating {
      0% {
        transform: translateY(0px);
      }
      50% {
        transform: translateY(-10px);
      }
      100% {
        transform: translateY(0px);
      }
    }
    #reader__scan_region img {
      animation: floating 2s ease-in-out infinite;
    }
    img[alt="Info icon"] {
      display: none; /* Oculta completamente el icono de información */
    }
      /* Animación para los puntos */
    @keyframes bounce {
      0%, 80%, 100% {
        transform: scale(0);
      }
      40% {
        transform: scale(1);
      }
    }

    /* Aplica estilos y animaciones a los puntos */
    .dot {
      display: inline-block;
      margin-left: 2px;
      animation: bounce 1.4s infinite;
    }

    .dot:nth-child(1) {
      animation-delay: 0s;
    }

    .dot:nth-child(2) {
      animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.4s;
  }
  </style>
</head>
<body>
  <div class="container text-center">
    <h1 class="display-4 mb-4" style="color: #333;">Escaneo QR</h1>
    <!-- Contenedor para la cámara -->
    <div id="reader" class="mx-auto"></div>
    <!-- Resultado del QR -->
    <p id="qr-result" class="mt-3">
      Esperando escaneo<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
    </p>
  </div>

  <!-- Librería de escaneo -->
  <script src="view/assets/js/html5-qrcode.min.js" type="text/javascript"></script>

  <script>
    let html5QrcodeScanner;

    function onScanSuccess(decodedText, decodedResult) {
      // Expresión regular para extraer la matrícula
      const validQrRegex = /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
      const match = decodedText.match(validQrRegex);

      if (match) {
        let matricula = match[1]; // Extrae solo el número de matrícula
        // ✅ QR válido
        document.getElementById("qr-result").innerText = `✅ Matrícula: ${matricula}`;
        console.log(`Matrícula detectada: ${matricula}`);

        // Simular clic en el botón "Detener escaneo"
        let stopButton = document.getElementById("html5-qrcode-button-camera-stop");
        if (stopButton) {
          stopButton.click();
        } else {
          console.warn("El botón 'Stop Scanning' no se encontró.");
        }
      } else {
        // ❌ QR inválido: continuar escaneando
        document.getElementById("qr-result").innerText = "❌ El QR no es correcto";
        console.warn("QR inválido detectado:", decodedText);
      }
    }

    function onScanFailure(error) {
      // Continúa escaneando aunque ocurra un error
      console.warn(`Error de escaneo: ${error}`);
    }

    function initializeScanner() {
      // Configuración avanzada
      const config = {
        fps: 5,
        // Ajusta el tamaño del recuadro de lectura
        qrbox: (viewfinderWidth, viewfinderHeight) => {
          const minSize = Math.min(viewfinderWidth, viewfinderHeight);
          return { width: minSize * 0.8, height: minSize * 0.8 };
        },
        videoConstraints: {
          facingMode: "environment",
          advanced: [{ focusMode: "continuous" }, { focusMode: "auto" }]
        },
        // IMPORTANTE: Esto **solo** habilita la cámara (no se muestra el botón de imagen)
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
      };

      // Inicializa el escáner con la configuración
      html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
      html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    // Para habilitar tap-to-focus manual (opcional)
    function enableTapToFocus(videoElement) {
      videoElement.addEventListener("click", async (event) => {
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
              console.log(`Autoenfoque aplicado en: (${x}, ${y})`);
            } catch (error) {
              console.error("Error al aplicar el autoenfoque:", error);
            }
          } else {
            console.warn("El enfoque manual no es compatible con esta cámara.");
          }
        }
      });
    }

    document.addEventListener("DOMContentLoaded", () => {
      initializeScanner();

      // Ajustar textos a español cuando la librería haya creado los botones:
      // Se hace con un pequeño 'setTimeout' para esperar que existan en el DOM
      setTimeout(() => {
        let startBtn = document.getElementById("html5-qrcode-button-camera-start");
        if (startBtn) {
          startBtn.innerText = "Iniciar escaneo";
        }

        let stopBtn = document.getElementById("html5-qrcode-button-camera-stop");
        if (stopBtn) {
          stopBtn.innerText = "Detener escaneo";
        }
      }, 1000);

      // Habilitar tap-to-focus (opcional)
      const observer = new MutationObserver(() => {
        const videoElement = document.querySelector("#reader video");
        if (videoElement) {
          enableTapToFocus(videoElement);
          observer.disconnect();
        }
      });
      observer.observe(document.getElementById("reader"), { childList: true, subtree: true });
    });
  </script>

  <!-- Bootstrap 5 JS y dependencias -->
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
  ></script>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"
  ></script>
</body>
</html>
