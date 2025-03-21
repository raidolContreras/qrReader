<?php
if (isset($_SESSION['sesion'])) {
    header('Location: ./');
    exit();
}
?>
<!-- Iconos de Font Awesome (opcional) -->
<script src="assets/js/f4781c35cc.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="assets/css/login.css">
<!-- Ola superior con animación -->
<div class="wave">
    <svg viewBox="0 0 500 150" preserveAspectRatio="none">
        <path
            class="shape-fill"
            d="M-4.19,39.56 C149.99,150.00 271.22,-49.99 503.36,66.86 L500.00,0.00 L0.00,0.00 Z"></path>
    </svg>
</div>

<!-- Contenedor de login -->
<div class="login-container">
    <!-- Logo de la universidad -->
    <img src="assets/images/logo-color.png" alt="UNIMO Universidad Montrer" class="logo" />

    <h1>Iniciar Sesión</h1>

    <div class="error-message"></div>

    <!-- Formulario de login -->
    <form id="loginForm">
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input
                type="email"
                name="email"
                id="email"
                placeholder="usuario@unimontrer.edu.mx"
                required />
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input
                type="password"
                name="password"
                id="password"
                placeholder="********"
                required />
        </div>
        <button type="submit">Ingresar</button>
    </form>

    <div class="extra-links">
        <a href="#">¿Olvidaste tu contraseña?</a>
    </div>
</div>

<!-- Scripts para el login -->
<script src="assets/js/login.js"></script>