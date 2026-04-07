<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'auction_php';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->conn->exec("SET NAMES utf8");
        } catch(PDOException $e) {
            die("Database Error: " . $e->getMessage() . "<br>Check if 'auction_php' database exists!");
        }
        return $this->conn;
    }
}
?>