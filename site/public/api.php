<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ApiController;
use App\Database\Connection;
use App\Repository\TemperatureReadingRepository;
use App\Repository\DoorReadingRepository;

try {
    $pdo = Connection::getInstance();
    $temperatureRepository = new TemperatureReadingRepository($pdo);
    $doorRepository = new DoorReadingRepository($pdo);
    
    $controller = new ApiController(
        $temperatureRepository,
        $doorRepository
    );

    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Could not fetch data.']);
}