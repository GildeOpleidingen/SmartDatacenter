<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\WebhookController;
use App\Database\Connection;
use App\Repository\SensorRepository;
// Removed: use App\Repository\TemperatureReadingRepository;
// Removed: use App\Repository\DoorReadingRepository;

try {
    $pdo = Connection::getInstance();
    $sensorRepository = new SensorRepository($pdo);
    
    // Instantiate controller with only the SensorRepository
    $controller = new WebhookController(
        $sensorRepository
    );

    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    error_log("Webhook Error: " . $e->getMessage());
    echo "Internal server error.";
}
