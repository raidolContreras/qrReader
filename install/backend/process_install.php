<?php
// install.php
header('Content-Type: application/json');

// Incluir dependencias básicas y clases de encriptación
require_once __DIR__ . "/../../controller/forms.controller.php";
require_once __DIR__ . "/../../model/forms.models.php";

// Incluir migraciones y el migrator
require_once __DIR__ . '/migrations/AbstractMigration.php';
require_once __DIR__ . '/migrations//CreateUsersTable.php';
require_once __DIR__ . '/migrations//CreateRoutesTable.php';
require_once __DIR__ . '/migrations//CreateScansTable.php';
require_once __DIR__ . '/Migrator.php';

// Recoger y sanitizar datos del formulario
$host            = isset($_POST['host']) ? trim($_POST['host']) : "";
$database        = isset($_POST['database']) ? trim($_POST['database']) : "";
$user_name       = isset($_POST['user_name']) ? trim($_POST['user_name']) : "";
$password        = isset($_POST['password']) ? trim($_POST['password']) : "";
$admin_nombre    = isset($_POST['admin_nombre']) ? trim($_POST['admin_nombre']) : "";
$admin_apellidos = isset($_POST['admin_apellidos']) ? trim($_POST['admin_apellidos']) : "";
$admin_email     = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : "";
$admin_password  = isset($_POST['admin_password']) ? trim($_POST['admin_password']) : "";

// Validaciones mínimas
if (empty($host) || empty($database) || empty($user_name) || empty($admin_nombre) || empty($admin_apellidos) || empty($admin_email) || empty($admin_password)) {
    echo json_encode([
        "success" => false,
        "message" => "Todos los campos son obligatorios."
    ]);
    exit;
}

// Si el archivo .env ya existe, se evita una doble instalación
if (file_exists(__DIR__ . '/../../.env')) {
    echo json_encode([
        "success" => false,
        "message" => "El sistema ya ha sido instalado."
    ]);
    exit;
}

// Generar master key y crear archivo .env
$master_key = SecureVault::generateSecretKey();
$envContent = "HOST={$host}\nDATABASE={$database}\nUSER_NAME={$user_name}\nPASSWORD={$password}\nMASTER_KEY={$master_key}\n";
if (file_put_contents(__DIR__ . '/../../.env', $envContent) === false) {
    echo json_encode([
        "success" => false,
        "message" => "No se pudo crear el archivo .env. Verifica permisos."
    ]);
    exit;
}

try {
    // Conectar al servidor MySQL (sin base de datos inicialmente)
    $pdo = new PDO("mysql:host={$host}", $user_name, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear la base de datos y seleccionarla
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8 COLLATE utf8_general_ci");
    $pdo->exec("USE `{$database}`");

    // Inicializar el migrator y agregar migraciones
    $migrator = new Migrator($pdo);
    $migrator->addMigration(new CreateUsersTable($pdo));
    $migrator->addMigration(new CreateRoutesTable($pdo));
    $migrator->addMigration(new CreateScansTable($pdo));

    // Ejecutar todas las migraciones
    $migrator->migrate();

    // === Seeder: Insertar datos iniciales ===

    // Encriptar datos para el administrador
    $admin_nombre_enc    = SecureVault::encryptData($admin_nombre, 'name');
    $admin_apellidos_enc = SecureVault::encryptData($admin_apellidos, 'name');
    $admin_email_enc     = SecureVault::encryptData($admin_email, 'email');
    $admin_password_enc  = SecureVault::encryptData($admin_password, 'password');
    $admin_role_enc      = SecureVault::encryptData('admin', 'role');

    // Encriptar datos para un usuario de prueba
    $user_nombre_enc     = SecureVault::encryptData('Prueba', 'name');
    $user_apellidos_enc  = SecureVault::encryptData('Usuario', 'name');
    $user_email_enc      = SecureVault::encryptData('usuario@prueba.com', 'email');
    $user_password_enc   = SecureVault::encryptData('Unimo2025+', 'password');
    $user_role_enc       = SecureVault::encryptData('usuario', 'role');

    $sqlInsert = "INSERT INTO users (nombre, apellidos, email, password, role)
                  VALUES (:nombre, :apellidos, :email, :password, :role)";
    
    // Insertar administrador
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->bindParam(":nombre", $admin_nombre_enc, PDO::PARAM_STR);
    $stmt->bindParam(":apellidos", $admin_apellidos_enc, PDO::PARAM_STR);
    $stmt->bindParam(":email", $admin_email_enc, PDO::PARAM_STR);
    $passwordHash = password_hash($admin_password_enc, PASSWORD_DEFAULT);
    $stmt->bindParam(":password", $passwordHash, PDO::PARAM_STR);
    $stmt->bindParam(":role", $admin_role_enc, PDO::PARAM_STR);
    $stmt->execute();
    $stmt->closeCursor();

    // Insertar usuario de prueba
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->bindParam(":nombre", $user_nombre_enc, PDO::PARAM_STR);
    $stmt->bindParam(":apellidos", $user_apellidos_enc, PDO::PARAM_STR);
    $stmt->bindParam(":email", $user_email_enc, PDO::PARAM_STR);
    $passwordHash = password_hash($user_password_enc, PASSWORD_DEFAULT);
    $stmt->bindParam(":password", $passwordHash, PDO::PARAM_STR);
    $stmt->bindParam(":role", $user_role_enc, PDO::PARAM_STR);
    $stmt->execute();
    $stmt->closeCursor();

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
