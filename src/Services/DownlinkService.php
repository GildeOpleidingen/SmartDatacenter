<?php
namespace App\Services;

use Dotenv\Dotenv;

class DownlinkService {
    public static function busylight(int $r, int $g, int $b, int $on, int $off) {
        $url = "https://eu1.cloud.thethings.network/api/v3/as/applications/gilde-datacenter/devices/busylight-001/down/replace";

        $data = [
            "downlinks" => [
                [
                    "f_port" => 15,
                    "decoded_payload" => [
                        "blue" => $b,
                        "green" => $g,
                        "offtime" => $off,
                        "ontime" => $on,
                        "red" => $r
                    ]
                ]
            ]
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    "Authorization: Bearer " . $_ENV['TTN_TOKEN'] . "\r\n" . 
                    "Content-Type: application/json"
                ],
                'content' => json_encode($data),
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        return [
            'response' => $response,
            'headers' => $http_response_header ?? []
        ];

    }
}