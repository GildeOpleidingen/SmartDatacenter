<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;
use App\Database\DBConn;
use PDO;

class Busylight {
    public function getCurrentColor() {
        $db = DBConn::getInstance();

        $sql = "SELECT * FROM busylightcolor LIMIT 1";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCurrentColor(int $r, int $g, int $b, int $state) {
        $db = DBConn::getInstance();

        if (!$this->getCurrentColor()) {
            $sql = "INSERT INTO busylightcolor(`red`, `green`, `blue`, `state`) VALUES (:red, :green, :blue, :state)";
        } else {
            $sql = "UPDATE busylightcolor SET `red` = :red, `green` = :green, `blue` = :blue, `state` = :state WHERE `id` = 1";
        }

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':red' => $r,
            ':green' => $g,
            ':blue' => $b,
            ':state' => $state,
        ]);
    }
}