<?php
// migrations/AbstractMigration.php

abstract class AbstractMigration {
    protected $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Aplica la migración
    abstract public function up();

    // Revierte la migración
    abstract public function down();
}
