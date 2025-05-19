<input type="hidden" id="idUser" value="<?php echo $_SESSION['idUser']; ?>">
<?php if (!isset($_SESSION['nameRoute'])): ?>

  <style>
    /* --- RESET BÁSICO ---------------------------------------------------- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%
    }

    /* --- VARIABLES DE TEMA ---------------------------------------------- */
    :root {
      --brand: #01643d;
      /* Verde institucional */
      --brand-dark: #014e2f;
      --bg: #f3f6fa;
      /* Fondo general */
      --surface: #ffffff;
      /* Tarjetas/controles */
      --radius: 1.2rem;
      --shadow: 0 4px 14px rgba(0, 0, 0, .08);
      --text: #1f2d3d;
    }

    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: var(--bg);
      color: var(--text);
      display: flex;
      flex-direction: column;
    }

    /* --- HEADER CON EFECTO BLUR ----------------------------------------- */
    .app-header {
      position: sticky;
      top: 0;
      z-index: 100;
      width: 100%;
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, .8);
      border-bottom: 1px solid #e2e8f0;
      text-align: center;
      padding: .7rem 1rem;
    }

    .app-header img {
      height: 44px;
      width: auto
    }

    /* --- BOTÓN FLOTANTE DE LOGOUT --------------------------------------- */
    .logout-fab {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 200;
      display: flex;
      align-items: center;
      gap: .5rem;
      background: #e53e3e;
      color: #fff;
      border: none;
      border-radius: var(--radius);
      padding: .55rem .95rem;
      font-size: .9rem;
      box-shadow: var(--shadow);
      cursor: pointer;
      transition: background .2s;
    }

    .logout-fab:hover {
      background: #f56565
    }

    /* --- GRID DE RUTAS --------------------------------------------------- */
    .routes-wrapper {
      width: min(1150px, 92%);
      margin: clamp(1.5rem, 5vw, 3rem) auto;
      display: grid;
      gap: clamp(1rem, 2vw, 1.5rem);
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }

    .route-tile {
      background: var(--surface);
      border-radius: var(--radius);
      height: 130px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: .7rem;
      font-weight: 500;
      font-size: .95rem;
      text-align: center;
      border: 2px solid transparent;
      box-shadow: var(--shadow);
      cursor: pointer;
      transition: transform .18s ease, border .18s ease;
    }

    .route-tile i {
      font-size: 1.8rem;
      color: var(--brand)
    }

    .route-tile:hover {
      transform: translateY(-4px)
    }

    .route-tile:active,
    .route-tile.active {
      border-color: var(--brand)
    }

    /* --- SKELETON PLACEHOLDER ------------------------------------------- */
    .skeleton {
      background: linear-gradient(90deg, #e4e7eb 25%, #f4f6f8 45%, #e4e7eb 65%);
      background-size: 200% 100%;
      animation: pulse 1.2s ease infinite;
      box-shadow: none;
    }

    @keyframes pulse {
      0% {
        background-position: 100% 0
      }

      100% {
        background-position: -100% 0
      }
    }

    /* --- TOAST ----------------------------------------------------------- */
    .toast {
      position: fixed;
      bottom: 1.2rem;
      left: 50%;
      transform: translate(-50%, 150%);
      padding: .9rem 1.4rem;
      border-radius: var(--radius);
      font-size: .9rem;
      color: #fff;
      opacity: 0;
      transition: opacity .4s, transform .4s;
      z-index: 999;
      box-shadow: var(--shadow);
    }

    .toast.show {
      opacity: 1;
      transform: translate(-50%, 0)
    }

    .toast.success {
      background: var(--brand)
    }

    .toast.error {
      background: #e53e3e
    }

    /* --- NAVEGACIÓN INFERIOR (MÓVIL) ------------------------------------ */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 60px;
      background: var(--surface);
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-around;
      align-items: center;
      box-shadow: 0 -2px 8px rgba(0, 0, 0, .04);
    }

    .bottom-nav a {
      color: #94a3b8;
      font-size: 1.4rem;
      position: relative;
      transition: color .2s;
    }

    .bottom-nav a.active {
      color: var(--brand)
    }

    .bottom-nav a .dot {
      position: absolute;
      bottom: -6px;
      left: 50%;
      width: 6px;
      height: 6px;
      background: var(--brand);
      border-radius: 50%;
      transform: translateX(-50%);
    }

    @media(min-width:576px) {
      .bottom-nav {
        display: none
      }
    }
  </style>
  <main>
    <section id="routesWrapper" class="routes-wrapper">
    </section>
  </main>
  <script>
    $(function() {

      /* ----- Cargar rutas ------------------------------------------------ */
      function loadRoutes() {
        $.post('controller/selectAction.php', {
            action: 'getRoutesToScan',
            idUser: $('#idUser').val()
          }, function(res) {
            const $w = $("#routesWrapper").empty();
            if (res.success && res.data.length) {
              // Ordenar rutas por nombre
              res.data.sort((a, b) => a.nameRoute.localeCompare(b.nameRoute));
              res.data.forEach(r => {
                let icon = "fa-route";
                if (r.registerType == 1) icon = "fa-person-walking";
                else if (r.registerType == 2) icon = "fa-car";
                else if (r.registerType == 3) icon = "fa-bus";

                const $tile = $(`
         <button class="route-tile">
           <i class="fas ${icon}"></i>
           <span>${r.nameRoute}</span>
         </button>
          `).data('id', r.idRoute)
                  .on('click', () => selectRoute($tile));

                $w.append($tile);
              });
              // Si solo hay una ruta, seleccionarla automáticamente
              if (res.data.length === 1) {
                selectRoute($w.find('.route-tile').first());
              }
            } else {
              $w.html('<div style="grid-column:1/-1;text-align:center;opacity:.7">No hay rutas disponibles</div>');
            }
          }, 'json')
          .fail(() => toast('Error al cargar rutas', 'error'));
      }

      /* ----- Seleccionar ruta ------------------------------------------- */
      function selectRoute($tile) {
        $.post('controller/selectAction.php', {
          action: 'selectRoute',
          routeId: $tile.data('id')
        }, res => {
          if (res.success) {
            $('.route-tile').removeClass('active');
            $tile.addClass('active');
            toast('Ruta seleccionada', 'success');
            setTimeout(() => location.href = 'qrScan', 600);
          } else {
            toast(res.message || 'Error', 'error');
          }
        }, 'json').fail(() => toast('Error de conexión', 'error'));
      }

      /* ----- Toast ------------------------------------------------------- */
      function toast(msg, type) {
        const $t = $(`<div class="toast ${type}">${msg}</div>`).appendTo('body');
        setTimeout(() => {
          $t.addClass('show')
        }, 50);
        setTimeout(() => {
          $t.removeClass('show')
        }, 3000);
        setTimeout(() => {
          $t.remove()
        }, 3500);
      }

      /* ----- Logout temporal (demo) ------------------------------------- */
      $('.logout').on('click', () => {
        // Implementa aquí tu lógica real de logout
        toast('Sesión cerrada', 'success');
      });

      /* ----- Inicio ------------------------------------------------------ */
      loadRoutes();
    });
  </script>
<?php else: ?>

  <!-- CONTENEDOR PRINCIPAL -->
  <main class="container row">
    <h1 id="routeName">Punto de registro: <?= $_SESSION['nameRoute']; ?></h1>

    <!-- Selector de rutas -->
    <div class="mb-3 col-12">
      <label for="routeSelect" class="form-label">Cambiar punto de acceso:</label>
      <select id="routeSelect" class="form-select"></select>
    </div>

    <div class="reader-button-wrapper" class="col-12">
      <div id="reader" style="display: none;"></div>
      <!-- <p id="qr-result"></p> -->
      <div id="reader-div">
        <button id="start">
          <div style="display: flex; flex-direction: column; align-items: center;">
            <img src="assets/images/qr-icon.png" alt="QR" style="width: 150px; height: 150px; margin-bottom: 8px;">
            <span>Iniciar escaneo</span>
          </div>
        </button>
      </div>
    </div>
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

  <script>
    loadRoutes();

    function loadRoutes() {
      $.post('controller/selectAction.php', {
          action: 'getRoutesToScan',
          idUser: $('#idUser').val()
        }, function(res) {
          const $select = $('#routeSelect').empty();
          if (res.success && res.data.length) {
            res.data.sort((a, b) => a.nameRoute.localeCompare(b.nameRoute));
            res.data.forEach(r => {
              const selected = (r.nameRoute === "<?= $_SESSION['nameRoute']; ?>") ? 'selected' : '';
              $select.append(`<option value="${r.idRoute}" ${selected}>${r.nameRoute}</option>`);
            });
          } else {
            $select.append('<option disabled selected>No hay rutas disponibles</option>');
          }
        }, 'json')
        .fail(() => toast('Error al cargar rutas', 'error'));
    }

    $('#routeSelect').on('change', function() {
      const selectedRoute = $(this).val();
      $.post('controller/selectAction.php', {
          action: 'selectRoute',
          routeId: selectedRoute
        }, res => {
          if (res.success) {
            location.reload();
          } else {
            toast(res.message || 'Error', 'error');
          }
        }, 'json')
        .fail(() => toast('Error de conexión', 'error'));
    });
  </script>
<?php endif; ?>