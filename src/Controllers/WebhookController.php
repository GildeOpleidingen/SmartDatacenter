<?php
namespace App\Controllers;

use App\Services\DBParser;
use App\Services\DownlinkService;

use Dotenv\Dotenv;

class WebhookController {
    public function handle() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            exit(json_last_error_msg());
        }

        $id = $data['end_device_ids']['device_id'] ?? null;
        $payload = $data['uplink_message']['decoded_payload'] ?? null;

        $parser = new DBParser();
        $parser->parseDataToDB($id, $payload);

        http_response_code(200);
        echo json_encode(["status" => "received"]);
    }

    public function status() {
        http_response_code(200);
        echo "api healthy";
    }
}