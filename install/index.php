<?php
// install.php

// Si ya existe el .env, redirigimos al index
if (file_exists(__DIR__ . '/../.env')) {
    header('Location: ./../');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Instalación del Sistema</title>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet" />
    <!-- Font Awesome para iconos (opcional) -->
    <script src="../assets/js/f4781c35cc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="main-container">
        <!-- Columna izquierda con fondo decorativo -->
        <div class="left-section">
            <div class="left-content">
                <h1>Sistema de gestión de asistencias por QR</h1>
                <p>Bienvenido al asistente de instalación. Completa los siguientes pasos para configurar tu sistema.</p>
            </div>
        </div>

        <!-- Columna derecha (contenedor flex que centra .right-content) -->
        <div class="right-section">
            <!-- Tarjeta que contiene la barra de progreso y el formulario -->
            <div class="right-content">

                <!-- Barra de progreso -->
                <div class="progress-bar">
                    <div class="progress" id="progress"></div>
                </div>

                <!-- Pasos de la instalación -->
                <div class="progress-steps">
                    <!-- Paso 1 -->
                    <div class="step active">
                        <i class="fas fa-database"></i>
                    </div>
                    <!-- Paso 2 -->
                    <div class="step">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <!-- Formulario -->
                <form id="installForm">
                    <!-- Paso 1: Configuración de Base de Datos -->
                    <div class="form-step active" id="step1">
                        <fieldset>
                            <legend>Configuración de la base de datos</legend>
                            <div class="form-group">
                                <label for="host">Host:</label>
                                <input
                                    type="text"
                                    name="host"
                                    id="host"
                                    placeholder="localhost"
                                    required />
                            </div>
                            <div class="form-group">
                                <label for="database">Nombre de la base de datos:</label>
                                <input
                                    type="text"
                                    name="database"
                                    id="database"
                                    placeholder="mi_base_de_datos"
                                    required />
                            </div>
                            <div class="form-group">
                                <label for="user_name">Nombre de usuario:</label>
                                <input
                                    type="text"
                                    name="user_name"
                                    id="user_name"
                                    placeholder="usuario"
                                    required />
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="********" />
                            </div>
                        </fieldset>
                        <div class="button-group">
                            <button type="button" class="btn-next">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 2: Datos del Administrador -->
                    <div class="form-step" id="step2">
                        <fieldset>
                            <legend>Datos del usuario administrador</legend>
                            <div class="form-group">
                                <label for="admin_nombre">Nombre:</label>
                                <input
                                    type="text"
                                    name="admin_nombre"
                                    id="admin_nombre"
                                    placeholder="Nombre"
                                    required />
                            </div>
                            <div class="form-group">
                                <label for="admin_apellidos">Apellidos:</label>
                                <input
                                    type="text"
                                    name="admin_apellidos"
                                    id="admin_apellidos"
                                    placeholder="Apellidos"
                                    required />
                            </div>
                            <div class="form-group">
                                <label for="admin_email">Email:</label>
                                <input
                                    type="email"
                                    name="admin_email"
                                    id="admin_email"
                                    placeholder="correo@ejemplo.com"
                                    required />
                            </div>
                            <div class="form-group">
                                <label for="admin_password">Contraseña:</label>
                                <input
                                    type="password"
                                    name="admin_password"
                                    id="admin_password"
                                    placeholder="********"
                                    required />
                            </div>
                            <div id="errorContainer" class="error-message"></div>
                        </fieldset>
                        <div class="button-group">
                            <button type="button" class="btn-prev">Anterior</button>
                            <button type="submit" id="btnSubmit">Instalar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tu script de instalación AJAX -->
    <script src="assets/js/install.js"></script>
    <script>
        $(document).ready(function() {
            // Avanzar a Paso 2
            $(".btn-next").click(function() {
                $("#step1").removeClass("active");
                $("#step2").addClass("active");
                $(".step").removeClass("active");
                $(".step:eq(1)").addClass("active");
                $("#progress").css("width", "100%");
            });

            // Retroceder a Paso 1
            $(".btn-prev").click(function() {
                $("#step2").removeClass("active");
                $("#step1").addClass("active");
                $(".step").removeClass("active");
                $(".step:eq(0)").addClass("active");
                $("#progress").css("width", "50%");
            });
        });
    </script>
</body>

</html>