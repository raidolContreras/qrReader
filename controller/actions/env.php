<?php
header('Content-Type: application/json');

// Ruta del archivo .env
$envFile = dirname(__DIR__, 2) . '/.env';

// Verificamos que el archivo exista y sea escribible
if (!file_exists($envFile) || !is_writable($envFile)) {
    echo json_encode(['success' => false, 'message' => 'El archivo .env no existe o no es escribible.']);
    exit;
}

// Leemos el archivo actual línea por línea
$lines = file($envFile, FILE_IGNORE_NEW_LINES);
$newLines = [];

foreach ($lines as $line) {
    // Si la línea es un comentario o está vacía, se mantiene igual
    if (empty(trim($line)) || strpos(trim($line), '#') === 0) {
        $newLines[] = $line;
        continue;
    }
    
    // Separamos la línea en clave y valor
    $parts = explode('=', $line, 2);
    $key = trim($parts[0]);
    
    // Si es MASTER_KEY, se conserva la línea original
    if ($key === 'MASTER_KEY') {
        $newLines[] = $line;
    } else {
        // Si se envía un valor para la clave, lo actualizamos; de lo contrario, se conserva el original
        $newValue = isset($_POST[$key]) ? $_POST[$key] : (isset($parts[1]) ? $parts[1] : '');
        $newLines[] = $key . '=' . $newValue;
    }
}

// Escribimos las nuevas líneas en el archivo .env
if (file_put_contents($envFile, implode(PHP_EOL, $newLines))) {
    echo json_encode(['success' => true, 'message' => 'Datos actualizados exitosamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo escribir en el archivo']);
}
