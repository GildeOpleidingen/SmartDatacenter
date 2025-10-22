<?php
// src/Repository/SensorRepository.php

namespace App\Repository;

use App\Database\Connection;
use PDO;
use PDOException;
use Exception;

/**
 * Beheert de database-interactie voor sensoren en hun activiteit.
 */
class SensorRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Zoekt alle unieke deviceID's op die activiteit in de 'activity' tabel hebben gelogd.
     * @return array Een array van unieke deviceID strings.
     * @throws Exception Als de SQL-query faalt.
     */
    public function findAllDeviceIdsWithActivity(): array
    {
        $sql = "
            SELECT DISTINCT d.deviceID
            FROM device AS d
            INNER JOIN activity AS a ON d.ID = a.device_ID
            ORDER BY d.deviceID ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            // Retourneert een array van alle waarden uit de eerste kolom (deviceID)
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); 
        } catch (PDOException $e) {
            // Gooit een gedetailleerde fout op als de query faalt
            throw new Exception("Fout bij ophalen device IDs: " . $e->getMessage() . " SQL: " . $sql);
        }
    }

    /**
     * Zoekt de laatst gelogde activiteit op voor een specifiek deviceID.
     * De data uit de 'data' kolom wordt automatisch samengevoegd met de databasekolommen.
     * * @param string $deviceId De deviceID van de sensor (bijv. 'powersocket-001').
     * @return object|null Een samengevoegd object of null als er geen data is gevonden.
     * @throws Exception Als de SQL-query faalt.
     */
    public function findLatestActivityByDeviceId(string $deviceId): ?object
    {
        $sql = "
            SELECT 
                a.ID, 
                d.deviceID, 
                a.data, 
                a.dateTime 
            FROM activity AS a
            INNER JOIN device AS d ON a.device_ID = d.ID
            WHERE d.deviceID = :deviceID
            ORDER BY a.dateTime DESC
            LIMIT 1
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':deviceID', $deviceId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return $this->processRow($row);
        } catch (PDOException $e) {
            throw new Exception("Fout bij ophalen laatste activiteit voor $deviceId: " . $e->getMessage() . " SQL: " . $sql);
        }
    }
    
    /**
     * Hulpmethode om de database-rij en de JSON 'data' kolom samen te voegen.
     * * @param array $row De ruwe database-rij.
     * @return object Het samengevoegde object met alle data.
     */
    private function processRow(array $row): object
    {
        // Decodeer de JSON string uit de 'data' kolom
        $jsonData = json_decode($row['data'] ?? '{}', true);

        // Map de relevante databasekolommen
        $result = [
            'ID' => $row['ID'],
            'deviceID' => $row['deviceID'],
            'dateTime' => $row['dateTime'],
        ];

        // Voeg de JSON-data samen met de database-data
        return (object) array_merge($result, $jsonData);
    }
}
