<?php

include "conection.php";

class FormsModel
{
    // Obtener los datos de la tabla DATOSALU
    static public function mdlGetDataStudent($matricula)
    {
        try {
            $conn = ConexionSil::conectar();
            $query = "SELECT * FROM DATOSALU WHERE MATRICULA = :matricula";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(":matricula", $matricula, PDO::PARAM_STR);
            $stmt->execute();
            $resultados = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultados;
        } catch (PDOException $e) {
            return "❌ Error al obtener datos: " . $e->getMessage();
        }
    }
}