<div class="sidebar" id="sidebar">
    <div class="logo">
        <a href="./" class="d-flex align-items-center justify-content-center mb-3">
            <img src="assets/images/logo-color.png" alt="Logo de la Empresa">
        </a>
    </div>
    <button class="btn btn-light w-100 mb-3" id="closeSidebar"><i class="fas fa-times"></i></button>
    <?php
    $currentPage = $_GET['pagina'] ?? 'analytics';
    ?>

    <a class="mb-1 <?php echo $currentPage == 'analytics' ? 'active' : ''; ?>" href="analytics"><i
            class="fas fa-chart-line"></i> Analíticas</a>
    <a class="mb-1 <?php echo $currentPage == 'users' ? 'active' : ''; ?>" href="users"><i class="fas fa-users"></i>
        Usuarios</a>
    <a class="mb-1 <?php echo $currentPage == 'pointRegisters' ? 'active' : ''; ?>" href="pointRegisters"><i
            class="fas fa-exchange-alt"></i> Puntos de Registro</a>
    <!-- <a class="mb-1 <?php  //echo $currentPage == 'buses' ? 'active' : ''; ?>" href="buses"><i class="fas fa-bus"></i> Buses</a> -->
</div>