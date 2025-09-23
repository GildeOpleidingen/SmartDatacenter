<?php

namespace App\Repository;

use PDO;

class TemperatureReadingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(int $sensorId, float $value): bool
    {
        $sql = "INSERT INTO temperature_readings (sensor_id, value, timestamp) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sensorId, $value, date('Y-m-d H:i:s')]);
    }

    public function findLatest(): ?array
    {
        $stmt = $this->pdo->query("
            SELECT t1.*, s.sensor_id
            FROM temperature_readings t1
            LEFT JOIN temperature_readings t2 ON (t1.sensor_id = t2.sensor_id AND t1.id < t2.id)
            INNER JOIN sensors s ON s.id = t1.sensor_id
            WHERE t2.id IS NULL
        ");
        return $stmt->fetch() ?: null;
    }
}