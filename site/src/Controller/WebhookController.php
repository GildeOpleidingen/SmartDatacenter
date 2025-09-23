<?php
// src/Controller/WebhookController.php

namespace App\Controller;

use App\Repository\DoorReadingRepository;
use App\Repository\SensorRepository;
use App\Repository\TemperatureReadingRepository;

class WebhookController
{
    private SensorRepository $sensorRepository;
    private TemperatureReadingRepository $temperatureRepository;
    private DoorReadingRepository $doorRepository;

    public function __construct(
        SensorRepository $sensorRepository,
        TemperatureReadingRepository $temperatureRepository,
        DoorReadingRepository $doorRepository
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->temperatureRepository = $temperatureRepository;
        $this->doorRepository = $doorRepository;
    }

    public function handleRequest(): void
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if (isset($data['end_device_ids']['device_id']) && isset($data['uplink_message']['decoded_payload'])) {
            $sensorId = $data['end_device_ids']['device_id'];
            $payload = $data['uplink_message']['decoded_payload'];

            $sensor = $this->sensorRepository->findBySensorId($sensorId);
            if (!$sensor) {
                $type = 'unknown';
                if (str_contains($sensorId, 'temp')) {
                    $type = 'temperature';
                } elseif (str_contains($sensorId, 'door')) {
                    $type = 'door';
                }
                $this->sensorRepository->create($sensorId, $type);
                $sensor_db_id = $this->sensorRepository->getLastInsertId();
            } else {
                $sensor_db_id = $sensor['id'];
            }

            foreach ($payload as $readingType => $value) {
                switch ($readingType) {
                    case 'temperature':
                        $this->temperatureRepository->save($sensor_db_id, (float)$value);
                        break;
                    case 'door_status':
                        $this->doorRepository->save($sensor_db_id, (bool)$value);
                        break;
                }
            }

            http_response_code(200);
            echo "Data processed.";
        } else {
            http_response_code(400);
            echo "Invalid data received.";
        }
    }
}