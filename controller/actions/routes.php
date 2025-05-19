<?php

switch ($_POST["action"]) {
    case 'getRoutes':
        $routes = FormsController::ctrGetRoutes();
        echo json_encode($routes);
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
        session_start();
        $_SESSION['route'] = $route['idRoute'];
        $_SESSION['nameRoute'] = $route['nameRoute'];
        $_SESSION['registerType'] = $route['registerType'];
        if ($route) {
            echo json_encode(['success' => true, 'data' => $route]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
        }
        break;
    case 'newRoute':
        $nameRoute = $_POST['nombre'];
        $encargados = $_POST['encargado'];
        $tipoRegistro = $_POST['tipoRegistro'];
        $result = FormsController::ctrNewRoute($nameRoute, $tipoRegistro);
        if ($result) {
            $idRoute = $result;
            foreach ($encargados as $idUser) {
                FormsController::ctrAssignUserToRoute($idUser, $idRoute);
            }
            echo json_encode(['success' => true, 'message' => 'Ruta creada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la ruta']);
        }
        break;
    case 'editRoute':
        $idRoute = $_POST['idRoute'];
        $nameRoute = $_POST['nameRoute'];
        $tipoRegistro = $_POST['tipoRegistro'];
        $result = FormsController::ctrEditRoute($idRoute, $nameRoute, $tipoRegistro);
        if ($result) {
            $encargados = $_POST['encargado'];
            FormsController::ctrUpdateRouteUsers($idRoute, $encargados);
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
    case 'getRoutesToScan':
        $idUser = $_POST['idUser'];
        $routes = FormsController::ctrGetRoutesToScan($idUser);
        if ($routes) {
            echo json_encode(['success' => true, 'data' => $routes]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No hay rutas disponibles']);
        }
        break;
}

function getRandomColor() {
    $letters = '0123456789ABCDEF';
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= $letters[mt_rand(0, 15)];
    }
    return $color;
}