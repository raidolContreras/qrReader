<script src="assets/js/popper.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/f4781c35cc.js" crossorigin="anonymous"></script>
<script src="assets/datatables/datatables.min.js"></script>


<script>
    $(document).ready(function() {
        $('#toggleSidebar').click(function() {
            $('#sidebar').toggleClass('active');
        });

        $('#closeSidebar').click(function() {
            $('#sidebar').removeClass('active');
        });
        // Si se presiona fuera de la pantalla se cierra el sidebar
        $(document).mouseup(function(e) {
            if (!$('#sidebar').is(e.target) && $('#sidebar').has(e.target).length === 0) {
                $('#sidebar').removeClass('active');
            }
        });

        // Abrir el modal al hacer clic en el botón de nuevo usuario
        $('.profile-menu').on('click', function(e) {
            // Evita que el clic se propague y cierre inmediatamente el menú
            e.stopPropagation();

            // Alterna la clase 'show' en el dropdown
            $(this).find('.dropdown-menu').toggleClass('show');
        });

        // Cerrar el dropdown al hacer clic fuera de él
        $(document).on('click', function() {
            $('.dropdown-menu.show').removeClass('show');
        });
    });
</script>