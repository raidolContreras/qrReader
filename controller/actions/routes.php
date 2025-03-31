<?php

switch ($_POST["action"]) {
    case 'getRoutes':
        $routes = FormsController::ctrGetRoutes();
        if ($routes) {
            echo json_encode(['success' => true, 'data' => $routes]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron rutas']);
        }
        break;
    case 'selectRoute':
        $idRoute = $_POST['routeId'];
        $route = FormsController::ctrGetRoute($idRoute);
        if ($route) {
            echo json_encode(['success' => true, 'data' => $route]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
        }
        break;
}
