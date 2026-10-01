<?php
namespace App\Controllers;

use App\Services\DBParser;
use App\Services\DeviceStateService;
use App\Services\DownlinkService;

use Dotenv\Dotenv;

class WebhookController {
    public function handle() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        error_log("TTN Webhook received: " . json_encode($data));
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            error_log("JSON Error: " . json_last_error_msg());
            exit(json_last_error_msg());
        }

        $id = $data['end_device_ids']['device_id'] ?? null;
        $payload = $data['uplink_message']['decoded_payload'] ?? null;

        error_log("Device ID: " . $id . ", Payload: " . json_encode($payload));

        $parser = new DBParser();
        $parser->parseDataToDB($id, $payload);

        if (is_array($payload) && array_key_exists('DOOR_OPEN_STATUS', $payload)) {
            error_log("Door sensor detected! Door status: " . $payload['DOOR_OPEN_STATUS']);
            $deviceState = new DeviceStateService();
            $result = $deviceState->updateBusylight();
            error_log("updateBusylight result: " . json_encode($result));
        } else {
            error_log("Payload is not array or missing DOOR_OPEN_STATUS");
        }

        http_response_code(200);
        echo json_encode(["status" => "received"]);
    }

    public function status() {
        http_response_code(200);
        echo "api healthy";
    }
}
