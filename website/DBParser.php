<?php
include_once 'DBConnection.php';
class DBParser {

    private $pdo;

    public function __construct() {
        $this->pdo = DBConn::getInstance();
    }

    public function storeActivity($json) {
        $stmt = $this->pdo->prepare("
            INSERT INTO activity (data, date)
            VALUES (?, NOW())
        ");
       $stmt->bindParam(1, $json);
       $stmt->execute();
    }
}
