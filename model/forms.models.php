<?php

include "conection.php";

class FormsModel
{
    static public function mdlNewUsers($data)
    {
        // Se obtiene la conexión PDO
        $pdo = Conexion::conectar();
        // Se define la consulta SQL para insertar los datos
        $sql = "INSERT INTO users (nombre, apellidos, email, password, role)
                  VALUES (:nombre, :apellidos, :email, :password, :role)";
        $stmt = $pdo->prepare($sql);

        // Se asignan los valores mediante bindParam
        $stmt->bindParam(":nombre", $data["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellidos", $data["apellidos"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $data["password"], PDO::PARAM_STR);
        $stmt->bindParam(":role", $data["role"], PDO::PARAM_STR);

        // Se ejecuta la consulta y se retorna el ID insertado o el error en caso de fallo
        if ($stmt->execute()) {
            return 'ok';
        } else {
            return $stmt->errorInfo();
        }
    }
}
