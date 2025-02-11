<?php
require_once __DIR__ . "/../view/assets/vendor/autoload.php";

class ConexionSil {
    static public function conectar() {


        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
        
        $serverName = $_ENV['DB_HOST'];
        $database = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
  
        try {
            $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
  }
  