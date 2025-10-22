<?php
// public/api.php

// 1. ZET ERROR REPORTING MAXIMAAL VOOR DEBUGGING (voor het geval de fout niet wordt opgevangen)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// 3. Zorg ervoor dat de klassen correct worden gebruikt
use App\Controller\ApiController;
use App\Database\Connection;
use App\Repository\SensorRepository;

try {
    // Deze regel is nu het focuspunt. Als deze faalt, wordt de fout correct gevangen door de Exception.
    $pdo = Connection::getInstance(); 
    
    $sensorRepository = new SensorRepository($pdo);
    
    $controller = new ApiController(
        $sensorRepository
    );

    $controller->handleRequest();

} catch (\Exception $e) {
    // 4. Stuur altijd JSON terug, zelfs bij een fout
    header('Content-Type: application/json');
    http_response_code(500);
    
    // Geef de gedetailleerde foutmelding. Dit is de verwachte JSON-respons bij een fout.
    echo json_encode([
        'error' => 'Could not fetch data. Critical PHP/DB Error.',
        // De message van de Connection.php vangt nu de PDO-fout op.
        'message' => $e->getMessage() 
    ]);
}
// Geen afsluitende ?>
