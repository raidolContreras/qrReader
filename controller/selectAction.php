<?php

require "forms.controller.php";
require "../model/forms.models.php";

// switch llego una peticion con un post llamado action
switch ($_POST["action"]) {
    case 'newUser':
        require "actions/users.php";
        break;
}
