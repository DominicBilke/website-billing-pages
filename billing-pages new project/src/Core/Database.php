<?php
namespace BillingSystem\Core;

class Database {
    private $pdo;
    private $statement;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new \PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function query($sql) {
        $this->statement = $this->pdo->prepare($sql);
        return $this;
    }

    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    public function execute() {
        return $this->statement->execute();
    }

    public function resultSet() {
        $this->execute();
        return $this->statement->fetchAll();
    }

    public function single() {
        $this->execute();
        return $this->statement->fetch();
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function debugDumpParams() {
        return $this->statement->debugDumpParams();
    }
} 