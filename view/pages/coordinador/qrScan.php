<!-- CONTENEDOR PRINCIPAL -->
<main class="container row">
  <div class="reader-button-wrapper" class="col-12">
    <div id="reader" style="display: none;"></div>
    <!-- <p id="qr-result"></p> -->
    <div id="reader-div">
      <button id="start">
        <i class="fas fa-camera"></i> Iniciar escaneo
      </button>
    </div>
  </div>
  <h1><?= $_SESSION['nameRoute']; ?></h1>
</main>

<!-- MODAL DE RESULTADOS -->
<div class="modal fade" data-bs-backdrop="static" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0" style="overflow: hidden; border-radius: 20px;">
      <!-- Encabezado oculto para diseño personalizado -->
      <div class="modal-header d-none"></div>
      <div class="modal-body p-0">
        <div id="modalResult"></div>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="confirmResult">Ingresar</button>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/qrReader/html5-qrcode.min.js"></script>
<script src="assets/js/qrReader/qrReader.js"></script>