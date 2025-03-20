<?php

header('Content-Type: application/json');

require_once __DIR__ . "/../../controller/forms.controller.php";
require_once __DIR__ . "/../../model/forms.models.php";

$master_key = SecureVault::generateSecretKey();

// Si ya existe el .env, enviamos un error y detenemos la ejecución
if (file_exists(__DIR__ . '/../../.env')) {
    echo json_encode([
        "success" => false,
        "message" => "El sistema ya ha sido instalado."
    ]);
    exit;
}

// Recoger y sanitizar datos del formulario
$host         = isset($_POST['host']) ? trim($_POST['host']) : "";
$database     = isset($_POST['database']) ? trim($_POST['database']) : "";
$user_name    = isset($_POST['user_name']) ? trim($_POST['user_name']) : "";
$password     = isset($_POST['password']) ? trim($_POST['password']) : "";

$admin_nombre     = isset($_POST['admin_nombre']) ? trim($_POST['admin_nombre']) : "";
$admin_apellidos  = isset($_POST['admin_apellidos']) ? trim($_POST['admin_apellidos']) : "";
$admin_email      = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : "";
$admin_password   = isset($_POST['admin_password']) ? trim($_POST['admin_password']) : "";

// Validaciones mínimas
if (empty($host) || empty($database) || empty($user_name) || empty($admin_nombre) || empty($admin_apellidos) || empty($admin_email) || empty($admin_password)) {
    echo json_encode([
        "success" => false,
        "message" => "Todos los campos son obligatorios."
    ]);
    exit;
}

// Crear el contenido del archivo .env
$envContent = "HOST={$host}\nDATABASE={$database}\nUSER_NAME={$user_name}\nPASSWORD={$password}\nMASTER_KEY={$master_key}\n";

if (file_put_contents(__DIR__ . '/../../.env', $envContent) === false) {
    echo json_encode([
        "success" => false,
        "message" => "No se pudo crear el archivo .env. Verifica permisos."
    ]);
    exit;
}

try {

    // encriptar datos
    $admin_nombre = SecureVault::encryptData($admin_nombre, 'name');
    $admin_apellidos = SecureVault::encryptData($admin_apellidos, 'name');
    $admin_email = SecureVault::encryptData($admin_email, 'email');
    $admin_password = SecureVault::encryptData($admin_password, 'password');
    $admin_role = SecureVault::encryptData('admin', 'role');

    // Conectar al servidor MySQL (sin especificar la base de datos)
    $pdo = new PDO("mysql:host={$host}", $user_name, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear la base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8 COLLATE utf8_general_ci");

    // Seleccionar la base de datos creada
    $pdo->exec("USE `{$database}`");

    // Crear la tabla de usuarios
    $sqlCreate = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        apellidos VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $pdo->exec($sqlCreate);

    // Insertar el usuario administrador
    $sqlInsert = "INSERT INTO users (nombre, apellidos, email, password, role)
                  VALUES (:nombre, :apellidos, :email, :password, :role)";
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->bindParam(":nombre", $admin_nombre, PDO::PARAM_STR);
    $stmt->bindParam(":apellidos", $admin_apellidos, PDO::PARAM_STR);
    $stmt->bindParam(":email", $admin_email, PDO::PARAM_STR);
    $passwordHash = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt->bindParam(":password", $passwordHash, PDO::PARAM_STR);
    $stmt->bindParam(":role", $admin_role, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Instalación completada exitosamente. Redirigiendo..."
    ]);
    exit;
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error en la instalación: " . $e->getMessage()
    ]);
    exit;
}
