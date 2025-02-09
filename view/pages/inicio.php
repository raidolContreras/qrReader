<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <!-- Se restringe el escalado para simular una app nativa -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Escaneo QR</title>
  <!-- Fuente moderna -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS (opcional si ya lo tienes en style.css) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="view/assets/css/style.css" rel="stylesheet" />
  <style>
    /* Estilos globales */
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #e0f7fa, #80deea);
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }
    /* Cabecera estilo app */
    .app-header {
      background-color: #01643d;
      color: #fff;
      padding: 15px;
      text-align: center;
      font-size: 1.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    /* Contenedor principal */
    .container {
      flex: 1;
      margin: 20px auto;
      max-width: 600px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
      padding: 20px;
    }
    /* Estilos para el lector QR */
    #reader {
      width: 100%;
      aspect-ratio: 4/3;
      margin-top: 20px;
      border: 2px solid #01643d;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
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
    /* Animación para elementos */
    @keyframes floating {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }
    #reader__scan_region img {
      animation: floating 2s ease-in-out infinite;
    }
    img[alt="Info icon"] {
      display: none;
    }
    @keyframes bounce {
      0%, 80%, 100% { transform: scale(0); }
      40% { transform: scale(1); }
    }
    .dot {
      display: inline-block;
      margin-left: 2px;
      animation: bounce 1.4s infinite;
    }
    .dot:nth-child(1) { animation-delay: 0s; }
    .dot:nth-child(2) { animation-delay: 0.2s; }
    .dot:nth-child(3) { animation-delay: 0.4s; }
    /* Ajustes responsivos */
    @media (max-width: 1024px) and (orientation: landscape) {
      .container { width: 80%; }
    }
    @media (max-width: 768px) and (orientation: portrait) {
      .container { width: 90%; }
    }
    @media (max-width: 480px) {
      .container { width: 100%; margin: 10px; padding: 15px; }
      .app-header { font-size: 1.2rem; padding: 10px; }
      #qr-result { font-size: 16px; }
      #start { width: 100%; }
    }
  </style>
</head>
<body>
  <!-- Cabecera tipo app móvil -->
  <header class="app-header">
    Escaneo QR
  </header>
  
  <!-- Contenedor principal -->
  <div class="container text-center">
    <!-- Contenedor para la cámara -->
    <div id="reader" style="display: none;"></div>
    <!-- Resultado del QR -->
    <p id="qr-result" style="display: none;">
      Esperando escaneo<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
    </p>
    <!-- Botón para iniciar el escaneo -->
    <button id="start" class="btn btn-primary mt-3">
      <i class="fas fa-camera"></i> Iniciar escaneo
    </button>
  </div>

  <!-- Librería de escaneo -->
  <script src="view/assets/js/html5-qrcode.min.js" type="text/javascript"></script>
  <script src="view/assets/js/qrReader.js"></script>
  <!-- jQuery y Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  