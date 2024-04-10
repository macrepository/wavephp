<?php
namespace Base\Model;

class Db {
    private $charset = 'utf8mb4';
    private $host = '';
    private $db = '';
    private $user = '';
    private $pass = '';

    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->host = $_ENV['DB_HOST'];
            $this->db   = $_ENV['DB_NAME'];
            $this->user = $_ENV['DB_USER'];
            $this->pass = $_ENV['DB_PASS'];

            $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
            $this->conn = new \PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // Set default fetch mode to FETCH_ASSOC
            $this->conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->conn->exec("set names $this->charset");
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Db();
        }
        return self::$instance;
    }

    public function getConn() {
        return $this->conn;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    private function __wakeup() {}
}
