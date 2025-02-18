<?php
// api.php
require_once "../forms.controller.php";
require_once "../../model/forms.models.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'getStudentData':
            if (isset($_POST['matricula'])) {
                $matricula = $_POST['matricula'];
                $response = FormsController::getStudentData($matricula);
                echo json_encode($response);
            } else {
                echo json_encode([
                    'error' => 'Missing matricula parameter'
                ]);
                die;
            }
            break;

        default:
            echo json_encode([
                'error' => 'No action specified'
            ]);
            break;
    }
    die;
    echo json_encode([
        'error' => 'Invalid action'
    ]);
}
