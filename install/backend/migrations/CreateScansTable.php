<?php
// migrations/CreateScansTable.php
require_once 'AbstractMigration.php';

class CreateScansTable extends AbstractMigration {
    public function up() {
        $sql = "CREATE TABLE IF NOT EXISTS scans (
            idScan INT AUTO_INCREMENT PRIMARY KEY,
            matricula INT(11) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            apellidos VARCHAR(100) NOT NULL,
            grupo VARCHAR(50) NOT NULL,
            grado INT(11) NOT NULL,
            nivel VARCHAR(50) NOT NULL,
            oferta VARCHAR(100) NOT NULL,
            idUser_scan INT(11) NOT NULL,
            routeScan INT(11) NOT NULL,
            dateScan TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            KEY idUser_scan (idUser_scan),
            KEY routeScan (routeScan),
            CONSTRAINT fk_scans_users FOREIGN KEY (idUser_scan) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_scans_routes FOREIGN KEY (routeScan) REFERENCES routes (idRoute) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->pdo->exec($sql);
    }
    
    public function down() {
        $this->pdo->exec("DROP TABLE IF EXISTS scans");
    }
}
