<?php

namespace core;

use PDO;

abstract class Model {

    private $pdo;

    protected $table;

    private $select = '*';

    private $where = 'TRUE';

    public function __construct() {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASSWORD'];

        $this->pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function select(...$items) {
        $this->select = implode(',', $items);
        return $this;
    }

    public function where($column, $method, $value) {
        $this->where = "`{$column}` {$method} '{$value}'";
        return $this;
    }

    public function find($id) {
        $sql = "SELECT {$this->select} FROM {$this->table} WHERE {$this->where} AND id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt->fetch();
    }

    public function get() {
        $sql = "SELECT {$this->select} FROM {$this->table} WHERE {$this->where}";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $row) {
        $columns = implode(',', array_map(function($column) {
            return "`{$column}`";
        }, array_keys($row)));

        $columns_placeholders = implode(',', array_map(function($column) {
            return ':' . $column;
        }, array_keys($row)));


        $sql = "INSERT INTO {$this->table}({$columns}) VALUES ({$columns_placeholders})";
        
        $stmt = $this->pdo->prepare($sql);

        foreach($row as $column => $value) {
            $stmt->bindValue(":{$column}", $value);
        }

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }
}