
<body>
  <!-- ENCABEZADO -->
  <header class="app-header">
    <img src="assets/images/logo-color.png" alt="Logo" width="150">
  </header>

  <!-- CONTENEDOR PRINCIPAL -->
  <main class="container row">
    <div class="reader-button-wrapper" class="col-12">
      <div id="reader" style="display: none;"></div>
      <!-- <p id="qr-result"></p> -->
      <div id="reader-div">
        <img width="250" src="assets/images/scan-qr.gif" alt="Escaneo basado en cámara" style="opacity: 1;">
        <button id="start">
          <i class="fas fa-camera"></i> Iniciar escaneo
        </button>
      </div>
    </div>
    <h1><?= $_SESSION['nameRoute']; ?></h1>
  </main>

  <!-- MODAL DE RESULTADOS -->
  <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content border-0" style="overflow: hidden; border-radius: 20px;">
        <!-- Encabezado oculto para diseño personalizado -->
        <div class="modal-header d-none"></div>
        <div class="modal-body p-0">
          <div id="modalResult"></div>
        </div>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER NAVIGATION (solo en móviles) -->
  <footer class="app-footer">
    <nav class="footer-nav">
      <a href="qrScan" class="nav-item active">
        <i class="fas fa-qrcode"></i>
        <span class="active-dot"></span> <!-- Punto indicador -->
      </a>
      <a href="routes" class="nav-item">
        <i class="fal fa-exchange-alt"></i>
        <span class="active-dot"></span>
      </a>
      <!-- <a href="profile" class="nav-item">
        <i class="fal fa-user"></i>
        <span class="active-dot"></span>
      </a> -->
      <a href="#" class="nav-item logout">
        <i class="fal fa-sign-out-alt"></i>
        <span class="active-dot"></span>
      </a>
    </nav>
  </footer>

  <!-- BOTÓN DE CERRAR SESIÓN -->
  <div class="logout-button-container">
    <a href="#" class="btn btn-danger logout-btn logout">
      <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
    </a>
  </div>

  <style>
    .logout-button-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 100;
    }
    
    .logout-btn {
      padding: 8px 15px;
      border-radius: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .logout-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    @media (max-width: 768px) {
      .logout-button-container {
        display: none; /* Hide on mobile since you have it in the footer nav */
      }
    }
  </style>

<script src="assets/js/qrReader/html5-qrcode.min.js"></script>
<script src="assets/js/qrReader/qrReader.js"></script>