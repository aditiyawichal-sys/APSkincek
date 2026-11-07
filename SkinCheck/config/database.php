<?php

class Database {
    private $host = "localhost";
    private $db_name = "skincheck_db";
    private $username = "root";
    private $password = ""; // XAMPP default is empty
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // MySQL connection string
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
