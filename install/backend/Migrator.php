<?php
// Migrator.php

class Migrator {
    protected $pdo;
    protected $migrations = [];
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Agregar una migración al arreglo
    public function addMigration(AbstractMigration $migration) {
        $this->migrations[] = $migration;
    }
    
    // Ejecuta todas las migraciones
    public function migrate() {
        foreach ($this->migrations as $migration) {
            $migration->up();
        }
    }
    
    // (Opcional) Revierte todas las migraciones
    public function rollback() {
        foreach (array_reverse($this->migrations) as $migration) {
            $migration->down();
        }
    }
}
