<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\WebhookController;
use App\Database\Connection;
use App\Repository\SensorRepository;
use App\Repository\TemperatureReadingRepository;
use App\Repository\DoorReadingRepository;

try {
    $pdo = Connection::getInstance();
    $sensorRepository = new SensorRepository($pdo);
    $temperatureRepository = new TemperatureReadingRepository($pdo);
    $doorRepository = new DoorReadingRepository($pdo);
    
    $controller = new WebhookController(
        $sensorRepository,
        $temperatureRepository,
        $doorRepository
    );

    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo "Internal server error.";
}