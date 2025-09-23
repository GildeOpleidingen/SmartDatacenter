<?php

namespace App\Repository;

use PDO;

class SensorRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findBySensorId(string $sensorId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM sensors WHERE sensor_id = ?");
        $stmt->execute([$sensorId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function create(string $sensorId, string $type): bool
    {
        $sql = "INSERT INTO sensors (sensor_id, type) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sensorId, $type]);
    }

    public function getLastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}