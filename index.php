<?php

// Verificar si el archivo .env existe
if (!file_exists(__DIR__ . '/.env')) {
    header('Location: /install/index.php');
    exit;
}

require_once "controller/template.controller.php";

require_once "controller/forms.controller.php";
require_once "model/forms.models.php";

$plantilla = new ControllerTemplate();
$plantilla -> ctrBringTemplate();