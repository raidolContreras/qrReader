<?php

require "forms.controller.php";
require "../model/forms.models.php";

// switch llego una peticion con un post llamado action
switch ($_POST["action"]) {
    case 'newUser':
    case 'getUsers':
    case 'getUsersToRoute':
    case 'login':
    case 'getUser':
    case 'editUser':
    case 'suspendUser':
    case 'reactivateUser':
    case 'deleteUser':
    case 'resetPassword':
        require "actions/users.php";
        break;
    default:
        echo json_encode(["success" => false, "message" => "Acción no válida"]);
        break;
    case 'editENV':
        require "actions/env.php";
        break;
    case 'logout':
        require "actions/logout.php";
        break;
    case 'getRoutes':
    case 'getRoutesToScan':
    case 'getRoute':
    case 'selectRoute':
    case 'newRoute':
    case 'editRoute':
    case 'deleteRoute':
        require "actions/routes.php";
        break;

    case 'registerStudent':
        require 'actions/registerStudent.php';
        break;

    case 'getStats':
    case 'getLogsScans':
        require 'actions/getStats.php';
        break;

    case 'newBus':
    case 'getBuses':
        require 'actions/Buses.php';
}
