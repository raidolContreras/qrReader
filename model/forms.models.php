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
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
		$stmt->closeCursor();
		$stmt = null;
		return $result;

    }

    static public function mdlSearchUser($item, $value) {
        $pdo = Conexion::conectar();
        $sql = "SELECT * FROM users WHERE $item = :$item";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":$item", $value);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$stmt = null;
		return $result;
    }

    static public function mdlGetUsers() {
        $pdo = Conexion::conectar();
        $sql = "SELECT * FROM users WHERE isActive = 1 ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlGetUser($id) {
        $pdo = Conexion::conectar();
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlEditUser($data) {
        $pdo = Conexion::conectar();
        $sql = "UPDATE users SET nombre = :nombre, apellidos = :apellidos, email = :email, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nombre", $data["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellidos", $data["apellidos"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindParam(":role", $data["role"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $data["id"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }
    
    static public function mdlDeleteUser($id) {
        $pdo = Conexion::conectar();
        $sql = "UPDATE users SET isActive = 0 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }
}
