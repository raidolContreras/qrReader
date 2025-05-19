<?php

switch ($_POST["action"]) {
    case 'getStats':

        $stats = FormsController::ctrGetStats();
        $qrStats = FormsController::ctrGetStatsByScans();
        $routesStats = FormsController::ctrGetStatsByRoutes();
        if ($stats) {
            echo json_encode(['success' => true, 'data' => $stats, 'qrStats' => $qrStats, 'routesStats' => $routesStats]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener estadísticas']);
        }
        break;
    case 'getLogsScans':
        $logs = FormsController::ctrGetLogsScans();
        foreach ($logs as &$log) {
            $log['nombreUsuario'] = SecureVault::decryptData($log['nombreUsuario']);
            $log['apellidosUsuario'] = SecureVault::decryptData($log['apellidosUsuario']);
            $log['role'] = SecureVault::decryptData($log['role']);
        }
            echo json_encode(['success' => true, 'data' => $logs]);
        
        break;
}
