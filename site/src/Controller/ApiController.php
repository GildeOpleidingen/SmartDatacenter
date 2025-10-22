<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Repository\SensorRepository;
use stdClass;

class ApiController
{
    private SensorRepository $sensorRepository;

    public function __construct(
        SensorRepository $sensorRepository
    ) {
        $this->sensorRepository = $sensorRepository;
    }

    /**
     * Handles the request, fetches the latest reading for ALL active sensors 
     * discovered in the database, and returns the results as a clean array of objects.
     */
    public function handleRequest(): void
    {
        $result = [];
        
        // 1. Get ALL unique device IDs that have recorded activity in the 'activity' table
        $activeDeviceIds = $this->sensorRepository->findAllDeviceIdsWithActivity();
        
        // 2. Fetch the latest reading object for each device
        foreach ($activeDeviceIds as $deviceId) {
            // The repository merges the database columns (like deviceID) and JSON keys (watt, volt, temp, status)
            $latestReading = $this->sensorRepository->findLatestActivityByDeviceId($deviceId);
            
            if ($latestReading) {
                // Return the complete object, ready for the JavaScript to interpret
                $result[] = $latestReading; 
            }
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
