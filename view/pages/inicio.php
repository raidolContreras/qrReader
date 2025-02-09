<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <!-- Para simular app nativa en móviles -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Escaneo QR</title>
  <!-- Fuente moderna (Roboto) -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS (opcional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    /* ==== RESET BÁSICO ==== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: rgb(219, 219, 219);
      /* Fondo rosado suave */
      color: #444;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ==== ENCABEZADO SIMIL APP ==== */
    .app-header {
      background-color: #1a5d22;
      /* Púrpura */
      color: #fff;
      padding: 15px;
      text-align: center;
      font-size: 1.5rem;
      font-weight: 500;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* ==== CONTENEDOR PRINCIPAL ==== */
    .container {
      flex: 1;
      margin: 20px auto;
      max-width: 500px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .container h2 {
      font-size: 1.2rem;
      font-weight: 500;
      margin-bottom: 10px;
      color: rgb(168, 168, 168);
    }

    /* ==== LECTOR QR ==== */
    #reader {
      width: 100%;
      /* aspect-ratio: 4/3; */
      /* Para asegurar proporción de cámara */
      margin-top: 20px;
      border: 2px dashedrgb(167, 167, 167);
      border-radius: 10px;
      position: relative;
    }
    /* reader landscape */
    @media (orientation: landscape) {
      #reader {
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      }
      .container {
        max-width: 900px;
      }
    }
    /* portrait */
    @media (orientation: portrait) {
     .reader-button-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
      }
    }

    #reader__scan_region img {
      max-width: 80%;
    }

    /* ==== RESULTADO QR ==== */
    #qr-result {
      margin-top: 20px;
      font-size: 1rem;
      color: rgb(46, 94, 40);
      text-align: center;
      min-height: 40px;
      display: none;
      /* Se mostrará dinámicamente */
    }

    /* Por defecto (portrait): el botón queda debajo (columna) */
    .reader-button-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      /* Centra el contenido horizontalmente */
      gap: 1rem;
      /* Separación entre el lector y el botón */
    }

    /* En modo landscape: el botón queda a un lado (fila) */
    @media (orientation: landscape) {
      .reader-button-wrapper {
        flex-direction: row;
        justify-content: center;
        align-items: center;
      }

      /* Opcionalmente ajustas el tamaño relativo del lector */
      #reader {
        width: 70%;
        /* Ejemplo: le das más espacio a la cámara */
      }

      #start {
        width: auto;
        /* Mantienes el botón en un tamaño adecuado */
        margin-top: 0;
        /* Elimina márgenes verticales si los tenías */
      }
    }

    /* ==== BOTÓN INICIAR ESCANEO ==== */
    #start {
      display: inline-block;
      padding: 12px 20px;
      font-size: 1rem;
      font-weight: 500;
      color: #fff;
      background-color: rgb(29, 77, 22);
      border: none;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(138, 43, 226, 0.4);
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 15px;
    }

    #start:hover {
      background-color: #7322bc;
      /* Oscurecer un poco al pasar el cursor */
    }

    /* ==== ANIMACIONES ==== */
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

    /* Ocultar ícono info por defecto */
    img[alt="Info icon"] {
      display: none;
    }

    @keyframes bounce {

      0%,
      80%,
      100% {
        transform: scale(0);
      }

      40% {
        transform: scale(1);
      }
    }

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

    /* ==== RESPONSIVE ==== */
    @media (max-width: 768px) {
      .container {
        width: 90%;
        margin-top: 30px;
      }

      .app-header {
        font-size: 1.3rem;
      }
    }
  </style>
</head>

<body>
  <!-- ENCABEZADO -->
  <header class="app-header">
    Escaneo QR
  </header>

  <!-- CONTENEDOR PRINCIPAL -->
  <div class="container text-center">
    <!-- Contenedor para la cámara -->

    <div class="reader-button-wrapper">
      <div id="reader" style="display: none;"></div>
      <p id="qr-result"></p>
      <button id="start">
        <i class="fas fa-camera"></i> Iniciar escaneo
      </button>
    </div>

  </div>

  <!-- Librería de escaneo -->
  <script src="view/assets/js/html5-qrcode.min.js" type="text/javascript"></script>
  <script src="view/assets/js/qrReader.js"></script>
  <!-- jQuery y Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>