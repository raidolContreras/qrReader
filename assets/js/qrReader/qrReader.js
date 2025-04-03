// === Aquí va tu código JS original (con las funciones de escaneo y zoom) ===
// === Copia y pega directamente tu contenido de qrReader.js o el script que compartiste. ===

// Variables globales para el escáner y ajustes de zoom
let html5QrcodeScanner;
let currentZoom = 1.0;
let videoTrack = null;
let failureCount = 0;
let lastZoomDirection = "in";
let autoFocusTimer = null;
let initialPinchDistance = null;
let initialZoom = currentZoom;

function onScanSuccess(decodedText, decodedResult) {
  const validQrRegex =
    /^https:\/\/sse\.unimontrer\.edu\.mx\/valides\.aspx\?matricula=(\d+)$/;
  const match = decodedText.match(validQrRegex);

  if (match) {
    let matricula = match[1];
    $("#qr-result")
      .html(`<span style="color: green;">✅ Matrícula: ${matricula}</span>`)
      .show();
    // Abrir modal #resultModal y cargar los datos
    $("#resultModal").modal("show");
    // Cargar los datos del estudiante
    mostrarVCard(matricula);
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
    $("#qr-result")
      .html('<span style="color: red;">❌ El QR no es correcto</span>')
      .show();
  }
  failureCount = 0;
}

function onScanFailure(error) {
  failureCount++;
  if (error.indexOf("No QR code found") !== -1) {
    $("#qr-result")
      .html(
        '<span style="color: orange;">⚠️ Ajustando enfoque automáticamente...</span>'
      )
      .show();
  } else {
    $("#qr-result")
      .html(
        '<span style="color: orange;">⚠️ Acerca o aleja la cámara para enfocar mejor</span>'
      )
      .show();
  }
}

async function tryZoom(track, direction = "in", step = 0.1) {
  try {
    const capabilities = track.getCapabilities();
    if (!capabilities.zoom) return;
    const min = capabilities.zoom.min || 1;
    const max = capabilities.zoom.max || 3;

    if (direction === "in") {
      currentZoom = Math.min(currentZoom + step, max);
    } else {
      currentZoom = Math.max(currentZoom - step, min);
    }
    await track.applyConstraints({
      advanced: [{ zoom: currentZoom }],
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
      direction = "in";
    } else if (currentZoom >= max - 0.1) {
      direction = "out";
    }
    await tryZoom(videoTrack, direction, 0.2);
    lastZoomDirection = direction === "in" ? "out" : "in";
    failureCount = 0;
  }
}

function initializeScanner() {
  const config = {
    fps: 10,
    qrbox: (w, h) => {
      const minSize = Math.min(w, h);
      return {
        width: minSize * 0.7,
        height: minSize * 0.7,
      };
    },
    videoConstraints: {
      facingMode: "environment",
    },
    // Oculta el "Seleccionar cámara"
    showCameraPicker: false,
    // Opcional: hace que recuerde la última cámara usada
    rememberLastUsedCamera: true,
  };

  html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);

  // El método render NO siempre regresa una promesa en versiones antiguas,
  // pero en las versiones más recientes, sí puede hacerlo.
  // Si no te funciona con 'then', puedes hacer tu lógica dentro de
  // onScanSuccess la primera vez que escanee o cuando estés seguro que se abrió la cámara.
  html5QrcodeScanner.render(onScanSuccess, onScanFailure);

  // Mensaje de estado
  $("#qr-result")
    .html(
      '<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>'
    )
    .show();
  $("#reader").show();
  $("#html5-qrcode-button-camera-start").html('<i class="fas fa-camera"></i> Iniciar escaneo')
  $("#reader-div").hide();

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
const DOUBLE_TAP_DELAY = 600;

$(document).ready(() => {
  const observer = new MutationObserver(() => {
    const videoElement = $("#reader video").get(0);
    if (videoElement) {
      if (
        videoElement.srcObject &&
        videoElement.srcObject.getVideoTracks().length > 0
      ) {
        videoTrack = videoElement.srcObject.getVideoTracks()[0];
      }
      $(videoElement).on("touchstart", async (e) => {
        if (e.touches.length === 1) {
          e.preventDefault();
          const currentTime = new Date().getTime();
          const tapLength = currentTime - lastTapTime;

          if (tapLength < DOUBLE_TAP_DELAY && tapLength > 0) {
            if (videoTrack) {
              await tryZoom(videoTrack, "out");
              $("#qr-result").html(
                '<span style="color: blue;">🔍 Alejando...</span>'
              );
            }
          } else {
            if (videoTrack) {
              await tryZoom(videoTrack, "in");
              $("#qr-result").html(
                '<span style="color: blue;">🔍 Acercando...</span>'
              );
            }
          }
          lastTapTime = currentTime;
          setTimeout(() => {
            $("#qr-result").html(
              '<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>'
            );
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
            const capabilities = videoTrack
              ? videoTrack.getCapabilities()
              : { zoom: { min: 1, max: 3 } };
            const min = capabilities.zoom.min || 1;
            const max = capabilities.zoom.max || 3;
            newZoom = Math.max(min, Math.min(max, newZoom));
            currentZoom = newZoom;
            if (videoTrack) {
              await videoTrack.applyConstraints({
                advanced: [{ zoom: currentZoom }],
              });
            }
            $("#qr-result").html(
              `<span style="color: blue;">🔍 Ajustando zoom: ${currentZoom.toFixed(
                1
              )}</span>`
            );
          }
        }
      });
      $(videoElement).on("touchend", (e) => {
        if (e.touches.length < 2) {
          initialPinchDistance = null;
          setTimeout(() => {
            $("#qr-result").html(
              '<span style="color: blue;">🔍 Escaneando... Toca o pellizca la pantalla para ajustar enfoque</span>'
            );
          }, 1000);
        }
      });
      observer.disconnect();
    }
  });
  observer.observe(document.getElementById("reader"), {
    childList: true,
    subtree: true,
  });
});

function mostrarVCard(matricula) {
  // Limpia o muestra el spinner en lo que llega la respuesta
  $("#modalResult").html(`
      <div class="d-flex justify-content-center py-4">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
    `);

  // Realiza la petición AJAX
  $.ajax({
    url: "https://infomontrer.unimontrer.edu.mx/api/api.php",
    method: "POST",
    data: {
      action: "getStudentData",
      matricula: matricula
    },
    success: function (response) {
      const data = typeof response === "string" ? JSON.parse(response) : response;
  
      // Construimos el HTML con un estilo tipo "vCard"
      const htmlContent = `
        <div class="vcard-container" style="position: relative; background-color: #f8f9fa;">
          
          <!-- Encabezado curvo con logo -->
          <div
            class="vcard-header"
            style="
              height: 120px;
              background: linear-gradient(135deg, rgb(73, 245, 159), rgb(21, 97, 52));
              clip-path: ellipse(120% 100% at 50% 0%);
              position: relative; 
              z-index: 10;
            "
          >
            <!-- LOGO con posición absoluta -->
            <img 
              src="assets/images/logo.png" 
              alt="Logo" 
              style="
                position: absolute; 
                top: 10px; 
                right: 15px;
                width: 120px; 
                height: auto;
              "
            />
          </div>
  
          <!-- Contenido de la tarjeta -->
          <div class="vcard-body text-center p-3" style="margin-top: -60px;">
            <!-- Foto circular -->
            <img
              src="https://sse.unimontrer.edu.mx/images/FOTOSESTUDIANTE/${data.MATRICULA}.jpg"
              alt="Foto de ${data.NOMBRE}"
              onerror="this.src='https://via.placeholder.com/150?text=Sin+Foto'"
              class="rounded-circle border border-2"
              style="
                width: 100px;
                height: 100px;
                object-fit: cover;
                background-color: #fff;
                position: relative;
                z-index: 11;
              "
            />
  
            <!-- Nombre completo -->
            <h5 class="mt-3 mb-1 fw-bold">
              ${data.NOMBRE} ${data.PATERNO} ${data.MATERNO}
            </h5>
  
            <!-- Descripción o datos principales -->
            <p class="text-muted mb-1" style="font-size: 0.9rem;">
              Matrícula: ${data.MATRICULA}
            </p>
            <p class="text-muted mb-3" style="font-size: 0.9rem;">
              Grado: ${data.GRADO} | Grupo ${data.GRUPO} <br/>
              Oferta academica: ${data.oferta}
            </p>
            
            <!-- Otros campos -->
            <div class="d-grid gap-2 mx-auto">
              <p class="text-muted mb-3" style="font-size: 0.9rem;">
                Acuerdo: ${data.acuerdo} | Clave: ${data.clave}
              </p>
            </div>
          </div>
        </div>
      `;
  
      // Inyectamos en modalResult
      $("#modalResult").html(htmlContent);
  
      // Abrimos el modal ya con todo listo
      $("#resultModal").modal("show");
    },
    error: function (xhr, status, error) {
      $("#modalResult").html(`
        <div class="alert alert-danger" role="alert">
          Error al obtener los datos del estudiante: ${error}
        </div>
      `);
      $("#resultModal").modal("show");
    },
  });
  
}



$('#html5-qrcode-button-camera-start').html('<i class="fas fa-camera"></i> Iniciar escaneo');