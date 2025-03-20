<?php
// install.php

// Si ya existe el .env, redirigimos al index
if (file_exists(__DIR__ . '/../.env')) {
    header('Location: ./');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Instalación del Sistema</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Instalación del Sistema</h1>
        <!-- Mensajes de respuesta -->
        <div id="message"></div>
        <form id="installForm">
            <fieldset>
                <legend>Configuración de la base de datos</legend>
                <div class="form-group">
                    <label for="host">Host:</label>
                    <input type="text" name="host" id="host" required>
                </div>
                <div class="form-group">
                    <label for="database">Nombre de la base de datos:</label>
                    <input type="text" name="database" id="database" required>
                </div>
                <div class="form-group">
                    <label for="user_name">Nombre de usuario:</label>
                    <input type="text" name="user_name" id="user_name" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password">
                </div>
            </fieldset>

            <fieldset>
                <legend>Datos del usuario administrador</legend>
                <div class="form-group">
                    <label for="admin_nombre">Nombre:</label>
                    <input type="text" name="admin_nombre" id="admin_nombre" required>
                </div>
                <div class="form-group">
                    <label for="admin_apellidos">Apellidos:</label>
                    <input type="text" name="admin_apellidos" id="admin_apellidos" required>
                </div>
                <div class="form-group">
                    <label for="admin_email">Email:</label>
                    <input type="email" name="admin_email" id="admin_email" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">Contraseña:</label>
                    <input type="password" name="admin_password" id="admin_password" required>
                </div>
                <div id="errorContainer" style="color:red; margin-top:10px;"></div>
            </fieldset>
            <button type="submit" id="btnSubmit">Instalar</button>
        </form>
    </div>
    <!-- Incluir el JS para el envío AJAX -->
    <script src="assets/js/install.js"></script>
</body>

</html>