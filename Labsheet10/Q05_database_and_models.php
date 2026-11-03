<?php
// Q5: core/Database.php (PDO singleton) + core/Model.php + models/Product.php

class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $c = require __DIR__ . '/../config/config.php';
        $dsn = "mysql:host={$c['db']['host']};dbname={$c['db']['name']};charset={$c['db']['charset']}";
        $this->pdo = new PDO($dsn, $c['db']['user'], $c['db']['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    private function __clone() {}
    public function __wakeup() { throw new Exception("Cannot unserialize singleton"); }

    public static function getInstance(): Database {
        return self::$instance ??= new Database();
    }
    public function getConnection(): PDO { return $this->pdo; }
}

abstract class Model {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(): array {
        return $this->db->query("SELECT * FROM {$this->table}")->fetchAll();
    }
    public function find(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public function create(array $data): int {
        $cols = implode(',', array_keys($data));
        $ph   = implode(',', array_fill(0, count($data), '?'));
        $s = $this->db->prepare("INSERT INTO {$this->table} ($cols) VALUES ($ph)");
        $s->execute(array_values($data));
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $data): bool {
        $set = implode(',', array_map(fn($c) => "$c = ?", array_keys($data)));
        $s = $this->db->prepare("UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?");
        return $s->execute([...array_values($data), $id]);
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $s->execute([$id]);
    }
}

class Product extends Model { protected string $table = 'products'; }
class User    extends Model { protected string $table = 'users'; }

/* SQL for the table:
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
Usage:
  $p = new Product(); $id = $p->create(['name'=>'Pen','price'=>10]);
  $p->update($id, ['price'=>12]); $p->find($id); $p->delete($id);
*/
