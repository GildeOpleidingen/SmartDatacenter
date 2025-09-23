<?php

namespace App\Repository;

use PDO;

class DoorReadingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(int $sensorId, bool $status): bool
    {
        $sql = "INSERT INTO door_readings (sensor_id, status, timestamp) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sensorId, $status, date('Y-m-d H:i:s')]);
    }

    public function findLatest(): ?array
    {
        $stmt = $this->pdo->query("
            SELECT d1.*, s.sensor_id
            FROM door_readings d1
            LEFT JOIN door_readings d2 ON (d1.sensor_id = d2.sensor_id AND d1.id < d2.id)
            INNER JOIN sensors s ON s.id = d1.sensor_id
            WHERE d2.id IS NULL
        ");
        return $stmt->fetch() ?: null;
    }
}