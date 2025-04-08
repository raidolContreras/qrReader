<?php

switch ($_POST["action"]) {
    case 'registerStudent':

        session_start();

        $data = array(
            "matricula" => $_POST['matricula'],
            "nombre" => $_POST['nombre'],
            "apellidos" => $_POST['apellidos'],
            "grupo" => $_POST['grupo'],
            "grado" => $_POST['grado'],
            "nivel" => $_POST['nivel'],
            "oferta" => $_POST['oferta']
        );
        $idUser = $_SESSION['idUser'];
        $route = $_SESSION['route'];

        $registerStudent = FormsController::ctrRegisterStudent($data, $idUser, $route);
        if ($registerStudent) {
            echo json_encode(['success' => true, 'message' => 'Estudiante registrado con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el estudiante']);
        }
        break;
}
