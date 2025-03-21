<?php

require "forms.controller.php";
require "../model/forms.models.php";

// switch llego una peticion con un post llamado action
switch ($_POST["action"]) {
    case 'newUser':
    case 'login':
        require "actions/users.php";
        break;
    default:
        echo json_encode(["success" => false, "message" => "Acción no válida"]);
        break;
}
