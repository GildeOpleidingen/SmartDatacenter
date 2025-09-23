<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Repository\DoorReadingRepository;
use App\Repository\TemperatureReadingRepository;

class ApiController
{
    private TemperatureReadingRepository $temperatureRepository;
    private DoorReadingRepository $doorRepository;

    public function __construct(
        TemperatureReadingRepository $temperatureRepository,
        DoorReadingRepository $doorRepository
    ) {
        $this->temperatureRepository = $temperatureRepository;
        $this->doorRepository = $doorRepository;
    }

    public function handleRequest(): void
    {
        $latestTemperature = $this->temperatureRepository->findLatest();
        $latestDoorStatus = $this->doorRepository->findLatest();
        
        $result = [];
        if ($latestTemperature) {
            $result[] = $latestTemperature;
        }
        if ($latestDoorStatus) {
            $result[] = $latestDoorStatus;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}