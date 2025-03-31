<?php
// migrations/CreateUsersTable.php
require_once 'AbstractMigration.php';

class CreateUsersTable extends AbstractMigration {
    public function up() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            apellidos VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            isActive TINYINT NOT NULL DEFAULT '1',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $this->pdo->exec($sql);
    }
    
    public function down() {
        $this->pdo->exec("DROP TABLE IF EXISTS users");
    }
}
