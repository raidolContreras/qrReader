<?php

switch ($_POST["action"]) {
    case 'newBus':
        newBus();
        break;
    case 'getBuses':
        getBuses();
        break;
}

function newBus()
{
    // Encriptar datos después de validarlos
    $numero = $_POST['numero'];

    $data = array(
        'numero' => $numero
    );

    // Guardar datos en la base de datos
    $saveBus = FormsController::ctrNewBus($numero);
    if ($saveBus == 'ok') {
        echo json_encode(['status' => 'success', 'message' => 'Bus Creado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $saveBus]);
    }
}

function getBuses()
{
    $buses = FormsController::ctrGetBuses();
    if ($buses) {
        echo json_encode(['status' => 'success', 'data' => $buses]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron buses']);
    }
}