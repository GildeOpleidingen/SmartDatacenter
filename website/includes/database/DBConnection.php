<?php
include_once 'DBConfiguration.php';
class DBConn extends DBConfig
{

    private $host = 'localhost';
    private $db   = 'smart-datacenter';
    private $user = 'root';
    private $pass = '';
    private $charset = "utf8mb4";

    private static $instance = null;
    private $pdo;

    private function __construct() {
        $hnc = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw an exception when an error occurs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false, // disables emulation of prepared statements
            PDO::ATTR_PERSISTENT         => true  //enables persistent connection.
        ];

        try {
            $this->pdo = new PDO($hnc, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Prevent cloning
    private function __clone() {}

    // Main entry point to get the PDO instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DBConn();
        }
        return self::$instance->pdo;
    }
}
