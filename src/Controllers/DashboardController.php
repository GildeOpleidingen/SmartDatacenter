<?php
namespace App\Controllers;

use App\Services\DBParser;
use App\Services\Definer;
use App\Services\DeviceStateService;

class DashboardController {
    public function index() {
        $parser = new DBParser();
        $definer = new Definer();
        $deviceStateService = new DeviceStateService();

        $deviceCards = $parser->generateCards();
        $fields = $definer->getFields();
        $initialDoorState = (int) $deviceStateService->getDoorState();

        require __DIR__ . '/../../views/Dashboard.php';
    }

    public function doorState() {
        $deviceStateService = new DeviceStateService();
        $state = (int) $deviceStateService->getDoorState();

        header('Content-Type: application/json');
        echo json_encode(['door_status' => $state]);
    }

    public function doorAlarm() {
        $payload = json_decode(file_get_contents('php://input'), true);
        $state = (int) ($payload['door_status'] ?? 0);

        $deviceStateService = new DeviceStateService();
        $deviceStateService->updateBusylight($state);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'door_status' => $state]);
    }
}
