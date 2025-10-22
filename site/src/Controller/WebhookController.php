<?php
// src/Controller/WebhookController.php

namespace App\Controller;

use App\Repository\SensorRepository;

class WebhookController
{
    private SensorRepository $sensorRepository;

    // We no longer need the specialized repositories
    public function __construct(
        SensorRepository $sensorRepository
        // Removed TemperatureReadingRepository $temperatureRepository, DoorReadingRepository $doorRepository
    ) {
        $this->sensorRepository = $sensorRepository;
    }

    public function handleRequest(): void
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        // Check for necessary keys from the incoming webhook
        if (isset($data['end_device_ids']['device_id']) && isset($data['uplink_message']['decoded_payload'])) {
            $deviceId = $data['end_device_ids']['device_id'];
            $payload = $data['uplink_message']['decoded_payload']; // This is the array of readings (e.g., ['temperature' => 20.5])
            $device = $this->sensorRepository->findByDeviceId($deviceId);
            
            $device_db_id = null;

            if (!$device) {
                // Device not found, attempt to create it
                // NOTE: The logic to determine type_ID and brand_ID from the deviceID string is complex
                // and depends on your database schema. For simplicity, we use placeholders (1).
                $typeId = 1;
                $brandId = 1;

                if ($this->sensorRepository->create($deviceId, $typeId, $brandId)) {
                    $device_db_id = (int)$this->sensorRepository->getLastInsertId();
                }
            } else {
                $device_db_id = (int)$device['ID'];
            }
            
            if ($device_db_id) {
                // --- Unified Saving ---
                // Save the entire payload as JSON data into the activity table
                if ($this->sensorRepository->saveActivity($device_db_id, $payload)) {
                    http_response_code(200);
                    echo "Data processed and saved for device {$deviceId}.";
                } else {
                    http_response_code(500);
                    echo "Failed to save activity data to database.";
                }
            } else {
                http_response_code(500);
                echo "Could not find or create device entry.";
            }
            
        } else {
            http_response_code(400);
            echo "Invalid data received from webhook.";
        }
    }
}
