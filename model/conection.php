<?php

require_once "./view/assets/vendor/autoload.php";

use Dotenv\Dotenv;

class ConexionSil
{
    static public function conectar()
    {
        // Cargar las variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $serverName = $_ENV['DB_HOST'] . ',' . $_ENV['DB_PORT'];
        $database = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

        try {
            $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("\u274C Error de conexión: " . $e->getMessage());
        }
    }
}
