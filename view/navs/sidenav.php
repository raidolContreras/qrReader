<div class="sidebar" id="sidebar">
    <div class="logo">
        <a href="./" class="d-flex align-items-center justify-content-center mb-3">
            <img src="assets/images/logo-color.png" alt="Logo de la Empresa">
        </a>
    </div>
    <button class="btn btn-light w-100 mb-3" id="closeSidebar"><i class="fas fa-times"></i></button>
    <?php
    $currentPage = basename($_SERVER['REQUEST_URI']);
    ?>

    <a class="mb-1 <?php echo $currentPage == 'users' ? 'active' : ''; ?>" href="users"><i class="fas fa-users"></i>
        Usuarios</a>
    <a class="mb-1 <?php echo $currentPage == 'routes' ? 'active' : ''; ?>" href="routes"><i
            class="fas fa-exchange-alt"></i> Rutas</a>
    <a class="mb-1 <?php echo $currentPage == 'analytics' ? 'active' : ''; ?>" href="analytics"><i
            class="fas fa-chart-line"></i> Analíticas</a>
</div>