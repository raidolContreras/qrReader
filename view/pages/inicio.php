<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escaneo QR</title>
  <link href="view/assets/css/style.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      width: 100%;
      max-width: 600px;
      padding: 20px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    #reader {
      width: 100%;
      aspect-ratio: 4/3;
      margin-top: 20px;
      border: 2px solid #01643d;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
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
      transition: background-color 0.3s ease;
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

    .btn-primary {
      background-color: #01643d;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #014e2c;
    }

    @media (max-width: 1024px) and (orientation: landscape) {
      .container {
        width: 60%;
      }
    }

    @media (max-width: 768px) and (orientation: portrait) {
      .container {
        width: 90%;
      }
    }

    @media (max-width: 480px) {
      .container {
        width: 100%;
        padding: 10px;
      }

      #reader {
        aspect-ratio: 3/4;
      }

      #qr-result {
        font-size: 16px;
      }

      .btn-primary {
        width: 100%;
        padding: 15px;
        font-size: 18px;
      }
    }
  </style>
</head>

<body>
  <div class="container text-center">
    <!-- Contenedor para la cámara -->
    <div id="reader" class="mx-auto" style="display: none;"></div>
    <!-- Resultado del QR -->
    <p id="qr-result" class="mt-3" style="display: none;">
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

  <!-- Bootstrap 5 JS y dependencias -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>