<?php 

class Conexion{

  static public function conectar(){


    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
    $dotenv->load();
    
    $HOST = $_ENV['HOST'];
    $DATABASE = $_ENV['DATABASE'];
    $USER_NAME = $_ENV['USER_NAME'];
    $PASSWORD = $_ENV['PASSWORD'];

    $link = new PDO("mysql:host=$HOST;dbname=$DATABASE", 
                $USER_NAME, 
                $PASSWORD);

    $link->exec("set names utf8");

    return $link;

  }

}