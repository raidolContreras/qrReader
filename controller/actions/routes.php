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
    case 'getRoute':
        $idRoute = $_POST['idRoute'];
        $route = FormsController::ctrGetRoute($idRoute);
        if ($route) {
            echo json_encode(['success' => true, 'data' => $route]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
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
    case 'newRoute':
        $nameRoute = $_POST['nombre'];
        $result = FormsController::ctrNewRoute($nameRoute);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Ruta creada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la ruta']);
        }
        break;
    case 'editRoute':
        $idRoute = $_POST['idRoute'];
        $nameRoute = $_POST['nameRoute'];
        $result = FormsController::ctrEditRoute($idRoute, $nameRoute);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Ruta editada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al editar la ruta']);
        }
        break;
    case 'deleteRoute':
        $idRoute = $_POST['idRoute'];
        $result = FormsController::ctrDeleteRoute($idRoute);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Ruta eliminada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la ruta']);
        }
        break;
}
