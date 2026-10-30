<?php
namespace App\Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DBConn
{
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../', '.env');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $db = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        //$charset = $_ENV['DB_CHARSET'];

        $hnc = "mysql:host={$host};dbname={$db};";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw an exception when an error occurs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false, // disables emulation of prepared statements
            PDO::ATTR_PERSISTENT         => true  //enables persistent connection.
        ];

        try {
            $this->pdo = new PDO($hnc, $user, $pass, $options);
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
