<?php $role = ($_SESSION["role"] == 'admin') ? 'Administrador': 'Moderador'; ?>
<nav class="navbar">
    <div class="container-fluid">
        <button class="btn btn-light d-md-none" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Menú de perfil -->
        <div class="profile-menu">
            <!-- Avatar pequeño que siempre se ve -->
            <img src="assets/images/user-avatar.png" alt="Usuario" class="profile-thumb">

            <!-- Dropdown -->
            <ul class="dropdown-menu">
                <!-- Encabezado dentro del dropdown -->
                <li class="dropdown-header">
                    <img src="assets/images/user-avatar.png" alt="Usuario" class="dropdown-avatar">
                    <span><?= $_SESSION["nombre"] . ' ' . $_SESSION["apellidos"] ?></span>
                    <!-- Rol -->
                     <span><?= $role ?></span>
                </li>
                <!-- Opciones del menú (puedes ajustar el texto/íconos a tu gusto) -->
                <li><a href="#"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configuración</a></li>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>