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

    static public function mdlSearchUser($item, $value)
    {
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

    static public function mdlGetUsers()
    {
        $pdo = Conexion::conectar();
        $sql = "SELECT * FROM users";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlGetUser($id)
    {
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

    static public function mdlEditUser($data)
    {
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

    static public function mdlSuspendUser($id)
    {
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

    static public function mdlReactivateUser($id)
    {
        $pdo = Conexion::conectar();
        $sql = "UPDATE users SET isActive = 1 WHERE id = :id";
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

    static public function mdlDeleteUser($id)
    {
        $pdo = Conexion::conectar();
        $sql = "DELETE FROM users WHERE id = :id AND isActive = 0";
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

    static public function mdlResetPassword($data)
    {
        $pdo = Conexion::conectar();
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":password", $data["password"], PDO::PARAM_STR);
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

    static public function mdlGetRoutes()
    {
        $pdo = Conexion::conectar();
        $sql = 'SELECT * FROM pointregisters WHERE isActive = 1 order by idRoute asc';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlGetUsersByRoute()
    {
        $pdo = Conexion::conectar();
        $sql = "
            SELECT
                p.idRoute,
                p.nameRoute,
                p.registerType,
                p.isActive,
                p.created_at,
                u.id       AS userId,
                u.nombre,
                u.apellidos,
                u.role
            FROM pointregisters p
            LEFT JOIN users_to_pointregisters up
                ON up.idPointregister = p.idRoute
            LEFT JOIN users u
                ON u.id = up.idUser
            WHERE p.isActive = 1 ORDER BY p.idRoute ASC;
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }

    static public function mdlGetRoute($idRoute)
    {
        $pdo = Conexion::conectar();
        $sql = 'SELECT *
                FROM pointregisters
                WHERE idRoute = :idRoute AND isActive = 1';
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

    static public function mdlNewRoute($nameRoute, $registerType)
    {
        $pdo = Conexion::conectar();
        date_default_timezone_set('America/Mexico_City');
        $created_at = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO pointregisters (nameRoute, created_at, registerType) VALUES (:nameRoute, :created_at, :registerType)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nameRoute', $nameRoute, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->bindParam(':registerType', $registerType, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = $pdo->lastInsertId();
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlAssignUserToRoute($idUser, $idPointregister)
    {
        $pdo = Conexion::conectar();
        $sql = 'INSERT INTO users_to_pointregisters (idUser, idPointregister) VALUES (:idUser, :idPointregister)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idPointregister', $idPointregister, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = 'ok';
        } else {
            $result = $stmt->errorInfo();
        }
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlUpdateRouteUsers($idRoute, $encargados)
    {
        $pdo = Conexion::conectar();
        // Primero eliminamos todas las asignaciones de usuarios a la ruta
        $sql = 'DELETE FROM users_to_pointregisters WHERE idPointregister = :idPointregister';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idPointregister', $idRoute, PDO::PARAM_INT);
        $stmt->execute();

        // Luego insertamos las nuevas asignaciones
        foreach ($encargados as $idUser) {
            self::mdlAssignUserToRoute($idUser, $idRoute);
        }
    }

    static public function mdlGetUsersByRouteOrRoutesByUser($idUser = null, $idPointregister = null)
    {
        $pdo = Conexion::conectar();

        if ($idUser !== null && $idPointregister === null) {
            // Buscar rutas asignadas a un usuario específico
            $sql = "SELECT 
                        p.idRoute,
                        p.nameRoute,
                        p.isActive,
                        p.created_at,
                        p.registerType
                    FROM pointregisters p
                    INNER JOIN users_to_pointregisters up ON up.idPointregister = p.idRoute
                    WHERE up.idUser = :idUser AND p.isActive = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        } else {
            // Buscar usuarios asignados a una ruta específica
            $sql = "SELECT 
                        u.id AS userId,
                        u.nombre,
                        u.apellidos,
                        u.role
                    FROM users u
                    INNER JOIN users_to_pointregisters up ON up.idUser = u.id
                    WHERE up.idPointregister = :idPointregister";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idPointregister', $idPointregister, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    static public function mdlEditRoute($idRoute, $nameRoute, $registerType)
    {
        $pdo = Conexion::conectar();
        $sql = 'UPDATE pointregisters SET nameRoute = :nameRoute, registerType = :registerType WHERE idRoute = :idRoute AND isActive = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nameRoute', $nameRoute, PDO::PARAM_STR);
        $stmt->bindParam(':registerType', $registerType, PDO::PARAM_INT);
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

    static public function mdlDeleteRoute($idRoute)
    {
        $pdo = Conexion::conectar();
        $sql = 'UPDATE pointregisters SET isActive = 0 WHERE idRoute = :idRoute';
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

    static public function mdlRegisterStudent($data, $idUser, $route)
    {
        $pdo = Conexion::conectar();

        date_default_timezone_set('America/Mexico_City');
        $dateScan = date('Y-m-d H:i:s');
        $sql = "INSERT INTO scans (matricula, nombre, apellidos, grupo, grado, nivel, oferta, idUser_scan, pointregisterscan, dateScan) 
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

    static  public function mdlGetStats()
    {
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

    static public function mdlGetStatsByScans()
    {
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

    static public function mdlGetStatsByRoutes()
    {
        $pdo = Conexion::conectar();
        $sql = "SELECT 
                    r.nameRoute AS route, 
                    COUNT(s.routeScan) AS total 
                FROM scans s 
                JOIN pointregisters r ON s.routeScan = r.idRoute 
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

    static public function mdlGetLogsScans()
{
    $pdo = Conexion::conectar();

    /*  Alias que coinciden con los headers de tu tabla            */
    $sql = "
        SELECT
            s.idScan,
            s.matricula,
            s.nombre,
            s.apellidos,
            CONCAT(s.grado, ' ', s.grupo)          AS grado_grupo,
            DATE_FORMAT(s.dateScan,'%Y-%m-%d %H:%i:%s') AS fecha_hora,
            r.registerType,
            r.nameRoute                            AS medio_transporte,
            r.nameRoute                            AS ubicacion,
            u.role,
            u.nombre  AS nombreUsuario,
            u.apellidos AS apellidosUsuario
        FROM scans s
        LEFT JOIN users          u ON u.id      = s.idUser_scan
        LEFT JOIN pointregisters r ON r.idRoute = s.pointregisterscan
        ORDER BY s.dateScan DESC;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $rows;
}

}
