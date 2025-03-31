<?php
// migrations/CreateRoutesTable.php
require_once 'AbstractMigration.php';

class CreateRoutesTable extends AbstractMigration {
    public function up() {
        $sql = "CREATE TABLE IF NOT EXISTS routes (
            idRoute INT AUTO_INCREMENT PRIMARY KEY,
            nameRoute VARCHAR(100) NOT NULL,
            isActive TINYINT NOT NULL DEFAULT '1',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->pdo->exec($sql);
    }
    
    public function down() {
        $this->pdo->exec("DROP TABLE IF EXISTS routes");
    }
}
