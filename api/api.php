<?php
// api.php
require_once "../controller/forms.controller.php";
require_once "../model/forms.models.php";

header("Content-Type: application/json");

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'getStudentData':
        if (isset($_POST['matricula'])) {
            $matricula = $_POST['matricula'];
            $response = FormsController::getStudentData($matricula);
            echo json_encode($response);
        } else {
            echo json_encode(["error" => "Falta el parámetro 'matricula'"]);
        }
        break;
    
    default:
        echo json_encode(["error" => "Acción no válida"]);
        break;
}