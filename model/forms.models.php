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

    static public function mdlGetRoutes() {
        $pdo = Conexion::conectar();
        $sql = 'SELECT * FROM routes WHERE isActive = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlGetRoute($idRoute) {
        $pdo = Conexion::conectar();
        $sql = 'SELECT * FROM routes WHERE idRoute = :idRoute AND isActive = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idRoute', $idRoute, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = $stmt->fetch();
            $stmt->closeCursor();
            $stmt = null;
            return $result;
        } else {
            $stmt->closeCursor();
            $stmt = null;
            return false; // Error en la consulta
        }
    }

    static public function mdlNewRoute($nameRoute) {
        $pdo = Conexion::conectar();
        date_default_timezone_set('America/Mexico_City');
        $created_at = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO routes (nameRoute, created_at) VALUES (:nameRoute, :created_at)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nameRoute', $nameRoute, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlEditRoute($idRoute, $nameRoute) {
        $pdo = Conexion::conectar();
        $sql = 'UPDATE routes SET nameRoute = :nameRoute WHERE idRoute = :idRoute AND isActive = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nameRoute', $nameRoute, PDO::PARAM_STR);
        $stmt->bindParam(':idRoute', $idRoute, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlDeleteRoute($idRoute) {
        $pdo = Conexion::conectar();
        $sql = 'UPDATE routes SET isActive = 0 WHERE idRoute = :idRoute';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idRoute', $idRoute, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlRegisterStudent($data, $idUser, $route) {
        $pdo = Conexion::conectar();
        
        date_default_timezone_set('America/Mexico_City');
        $dateScan = date('Y-m-d H:i:s');
        $sql = "INSERT INTO scans (matricula, nombre, apellidos, grupo, grado, nivel, oferta, idUser_scan, routeScan, dateScan) 
                VALUES (:matricula, :nombre, :apellidos, :grupo, :grado, :nivel, :oferta, :idUser, :routeScan, :dateScan)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":matricula", $data["matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre", $data["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellidos", $data["apellidos"], PDO::PARAM_STR);
        $stmt->bindParam(":grupo", $data["grupo"], PDO::PARAM_STR);
        $stmt->bindParam(":grado", $data["grado"], PDO::PARAM_STR);
        $stmt->bindParam(":nivel", $data["nivel"], PDO::PARAM_STR);
        $stmt->bindParam(":oferta", $data["oferta"], PDO::PARAM_STR);
        $stmt->bindParam(":idUser", $idUser, PDO::PARAM_INT);
        $stmt->bindParam(":routeScan", $route, PDO::PARAM_INT);
        $stmt->bindParam(":dateScan", $dateScan, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $result = true;
        } else {
            $result = false;
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static  public function mdlGetStats() {
        $pdo = Conexion::conectar();
        $sql = "SELECT 
                (SELECT SUM(1) FROM scans) AS totalScans,
                (SELECT COUNT(*) FROM users WHERE isActive = 1) AS activeUsers,
                (SELECT MAX(dateScan) FROM scans) AS lastScanTime;";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt = null;
            return $result;
        } else {
            return false; // Error en la consulta
        }
    }

    static public function mdlGetStatsByScans() {
        $pdo = Conexion::conectar();
        $sql = "SELECT 
                    DAYNAME(dateScan) AS day, 
                    COUNT(*) AS total 
                FROM scans 
                WHERE WEEK(dateScan) = WEEK(CURDATE()) 
                GROUP BY DAYNAME(dateScan) 
                ORDER BY FIELD(DAYNAME(dateScan), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute()) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt = null;
            return $result;
        } else {
            $stmt->closeCursor();
            $stmt = null;
            return false; // Error en la consulta
        }
    }

    static public function mdlGetStatsByRoutes() {
        $pdo = Conexion::conectar();
        $sql = "SELECT 
                    r.nameRoute AS route, 
                    COUNT(s.routeScan) AS total 
                FROM scans s 
                JOIN routes r ON s.routeScan = r.idRoute 
                WHERE s.dateScan >= DATE_SUB(NOW(), INTERVAL 1 WEEK) 
                GROUP BY r.nameRoute";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute()) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt = null;
            return $result;
        } else {
            $stmt->closeCursor();
            $stmt = null;
            return false; // Error en la consulta
        }
    }

    static public function mdlGetLogsScans() {
        $pdo = Conexion::conectar();
        $sql = "SELECT s.*, u.nombre AS nombreUsuario, u.apellidos AS apellidosUsuario, r.nameRoute FROM scans s LEFT JOIN users u ON s.idUser_scan = u.id LEFT JOIN routes r ON r.idRoute = s.routeScan ORDER BY dateScan DESC;";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute()) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt = null;
            return $result;
        } else {
            $stmt->closeCursor();
            $stmt = null;
            return false; // Error en la consulta
        }
    }
}
