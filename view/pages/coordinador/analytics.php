<style>
    /* --- DataTables: alineación de botones + buscador --- */
    .dataTables_wrapper .dt-buttons {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        margin-bottom: 0;
    }

    .dataTables_wrapper .dt-buttons .btn {
        padding: .45rem 1rem;
    }

    .dataTables_wrapper .dt-search {
        display: flex;
        align-items: center;
        margin-left: auto;
        flex-wrap: wrap;
    }

    .dataTables_wrapper .dt-search label {
        margin-bottom: 0;
        font-weight: 500;
        gap: .5rem;
    }

    .dataTables_wrapper .dt-search input[type="search"].dt-input {
        width: 220px;
    }

    @media (max-width: 575.98px) {

        .dataTables_wrapper .dt-search,
        .dataTables_wrapper .dt-buttons {
            width: 100%;
            justify-content: flex-start;
            margin-top: .5rem;
        }

        .dataTables_wrapper .dt-search input[type="search"].dt-input {
            width: 100%;
        }
    }

    /* Ajuste para los filtros encima de la tabla */
    .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filter-group .form-group {
        min-width: 150px;
    }
</style>

<div class="content">
    <div class="container-fluid py-4">
        <!-- Log Table Card -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">

                        <!-- filtros con SELECT dinámicos -->
                        <div class="filter-group mb-3">
                            <div class="form-group">
                                <label for="filter-fecha" class="form-label">Filtrar por Fecha:</label>
                                <select id="filter-fecha" class="form-select">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filter-medio" class="form-label">Filtrar por Medio:</label>
                                <select id="filter-medio" class="form-select">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filter-ubicacion" class="form-label">Filtrar por Ubicación:</label>
                                <select id="filter-ubicacion" class="form-select">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filter-grado" class="form-label">Filtrar por Grado/Grupo:</label>
                                <select id="filter-grado" class="form-select">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <h5 class="mb-0 fw-bold">Bitácora de Escaneos</h5>
                            <button class="btn btn-success btn-sm" onclick="table.ajax.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="scan-logs-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Matrícula</th>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>Grado y Grupo</th>
                                        <th>Registrado por</th>
                                        <th>Medio de transporte</th>
                                        <th>Ubicación</th>
                                        <th>Fecha y hora</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Log Table Card -->
            </div>
        </div>
        <!-- End Row -->
    </div>
</div>
<script>
  // Función global para capitalizar la primera letra
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  $(document).ready(function() {
    // Inicializamos DataTable
    const table = $('#scan-logs-table').DataTable({
      dom:
        "<'row mb-3'<'col-12 d-flex flex-wrap align-items-start'<'dt-buttons'B><'dt-search'f>>>" +
        "t" +
        "<'row mt-2'<'col-12 col-md-6'i><'col-12 col-md-6'p>>",
      buttons: [
        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm' },
        { extend: 'pdfHtml5',   text: 'PDF',   className: 'btn btn-danger  btn-sm' }
      ],
      processing: true,
      serverSide: false,
      deferRender: true,

      ajax: {
        url: 'controller/selectAction.php',
        type: 'POST',
        data: { action: 'getLogsScans' },
        dataSrc: json => json.success ? json.data : []
      },

      columns: [
        { data: null, render: (d, t, r, m) => m.row + 1 },
        { data: 'matricula' },
        { data: 'nombre' },
        { data: 'apellidos' },
        { data: 'grado_grupo' },
        { data: null, render: d => `${d.nombreUsuario} ${d.apellidosUsuario}` },
        { data: 'medio_transporte' },
        { 
          data: null, 
          render: d => 
            d.role === 'chofer' 
              ? 'Las Americas' 
              : ((d.role === 'coordinador' && d.registerType === 3)  ? 'Campus Lázaro Cárdenas' : d.ubicacion) 
        },
        {
          data: 'fecha_hora',
          render: function(data, type) {
            if (type === 'display' || type === 'filter') {
              const dt = new Date(data);
              const opcionesFecha = {
                weekday: 'long',
                day:     'numeric',
                month:   'long',
                year:    'numeric'
              };
              const fechaStr = dt.toLocaleDateString('es-ES', opcionesFecha);
              const horaStr  = dt.toLocaleTimeString('es-ES', {
                hour:   'numeric',
                minute: '2-digit',
                hour12: true
              });
              return capitalize(fechaStr) + ', ' + horaStr;
            }
            return data;
          }
        }
      ],

      order: [[8, 'desc']],
      language: { url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json' },

      initComplete: function() {
  const api  = this.api();
  const data = api.rows({ search: 'applied' }).data().toArray();
  const unique = arr => [...new Set(arr)].sort();

  // 1. Definimos la forma en que queremos el label
  const opcionesFecha = {
    weekday: 'long',
    day:     'numeric',
    month:   'long',
    year:    'numeric'
  };

  // 2. Mapear cada registro a su label (sin hora) y luego uniquear
  const fechasLabels = unique(
    data.map(r => {
      const dt = new Date(r.fecha_hora);
      // toLocaleDateString ya quita la hora
      const lbl = dt.toLocaleDateString('es-ES', opcionesFecha);
      // capitalizar la primera letra
      return lbl.charAt(0).toUpperCase() + lbl.slice(1);
    })
  );

  // Rellenar los selects (ahora ya no necesitamos 'isFecha')
  fillSelect('#filter-fecha',     fechasLabels);
  fillSelect('#filter-medio',     unique(data.map(r => r.medio_transporte)));
  fillSelect('#filter-ubicacion', unique(data.map(r =>
    r.role === 'chofer'
      ? 'Las Americas'
      : ((r.role === 'coordinador' && r.registerType === 3)
          ? 'Campus Lázaro Cárdenas'
          : r.ubicacion)
  )));
  fillSelect('#filter-grado',     unique(data.map(r => r.grado_grupo)));
},
    });

    /**
     * Rellena un <select> dado con opciones.
     * Si isFecha es true, usa el texto formateado como value y label,
     * para que el .search() coincida con lo que muestra la celda.
     */
    function fillSelect(selector, list, isFecha) {
      const $sel = $(selector).empty().append('<option value="">Todos</option>');
      list.forEach(val => {
        if (isFecha) {
          const dt = new Date(val);
          const fechaLabel = dt.toLocaleDateString('es-ES', {
            weekday: 'long',
            day:     'numeric',
            month:   'long',
            year:    'numeric'
          });
          const label = capitalize(fechaLabel);
          $sel.append(`<option value="${label}">${label}</option>`);
        } else {
          $sel.append(`<option value="${val}">${val}</option>`);
        }
      });
    }

    // Listeners para filtrar en tiempo real
    $('#filter-fecha').on('change',     () => table.column(8).search($('#filter-fecha').val()).draw());
    $('#filter-medio').on('change',     () => table.column(6).search($('#filter-medio').val()).draw());
    $('#filter-ubicacion').on('change', () => table.column(7).search($('#filter-ubicacion').val()).draw());
    $('#filter-grado').on('change',     () => table.column(4).search($('#filter-grado').val()).draw());
  });
</script>
