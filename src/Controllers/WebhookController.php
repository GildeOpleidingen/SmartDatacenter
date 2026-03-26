<?php
namespace App\Controllers;

use App\Services\DBParser;

class WebhookController {
    public function handle() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            exit(json_last_error_msg());
        }

        $parser = new DBParser();
        $parser->parseDataToDB($data);

        http_response_code(200);
        echo json_encode(["status" => "received"]);
    }
}