<script src="assets/js/popper.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/f4781c35cc.js" crossorigin="anonymous"></script>
<script src="assets/datatables/datatables.min.js"></script>


<script>
    $(document).ready(function () {
        $('#toggleSidebar').click(function () {
            $('#sidebar').toggleClass('active');
        });

        $('#closeSidebar').click(function () {
            $('#sidebar').removeClass('active');
        });
        // Si se presiona fuera de la pantalla se cierra el sidebar
        $(document).mouseup(function (e) {
            if (!$('#sidebar').is(e.target) && $('#sidebar').has(e.target).length === 0) {
                $('#sidebar').removeClass('active');
            }
        });

        // Abrir el modal al hacer clic en el botón de nuevo usuario
        $('.profile-menu').on('click', function (e) {
            // Evita que el clic se propague y cierre inmediatamente el menú
            e.stopPropagation();

            // Alterna la clase 'show' en el dropdown
            $(this).find('.dropdown-menu').toggleClass('show');
        });

        // Cerrar el dropdown al hacer clic fuera de él
        $(document).on('click', function () {
            $('.dropdown-menu.show').removeClass('show');
        });

        // Función para crear tooltip
        function createTooltip(element) {
            // Crear elemento tooltip si no existe
            if ($(element).find('.tooltip').length === 0) {
                const tooltipText = $(element).data('tooltip');
                const tooltip = $('<span class="tooltip"></span>').text(tooltipText);
                $(element).append(tooltip);
            }
        }

        // Evento para mostrar tooltip al pasar el cursor
        $(document).on('mouseenter', '.tooltip-btn', function () {
            createTooltip(this);
            $(this).find('.tooltip').addClass('visible');
        });

        // Evento para ocultar tooltip al quitar el cursor
        $(document).on('mouseleave', '.tooltip-btn', function () {
            $(this).find('.tooltip').removeClass('visible');
        });

        // Para tablas dinámicas (DataTables)
        if ($.fn.DataTable) {
            $('.dataTable').on('draw.dt', function () {
                $('.tooltip').remove(); // Limpiar tooltips viejos al redibujar la tabla
            });
        }
        
    $('.logout').on('click', function(e) {
        e.preventDefault(); // Evita que el enlace recargue o navegue
        $.ajax({
            url: 'controller/selectAction.php',
            type: 'POST',
            data: { action: 'logout' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect;
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});
</script>