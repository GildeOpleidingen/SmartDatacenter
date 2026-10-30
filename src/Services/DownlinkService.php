<?php
namespace App\Services;

use Dotenv\Dotenv;

class DownlinkService {
    public static function busylight(int $r, int $g, int $b, int $on, int $off) {
        $token = $_ENV['TTN_TOKEN'] ?? getenv('TTN_TOKEN') ?? null;
        $url = $_ENV['TTN_DOWNLINK_URL'] ?? getenv('TTN_DOWNLINK_URL') ?? null;

        if (!$token || !$url) {
            return [
                'success' => false,
                'response' => 'No good',
                'status' => 0,
                'headers' => []
            ];
        }

        $payload = pack('C5', $r, $g, $b, $on, $off);

        $data = [
            'downlinks' => [
                [
                    'f_port' => 15,
                    'frm_payload' => base64_encode($payload),
                    'priority' => 'NORMAL',
                    'confirmed' => false
                ]
            ]
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/json'
                ],
                'content' => json_encode($data),
                'ignore_errors' => true,
                'timeout' => 15
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        $status = 0;
        if (!empty($http_response_header)) {
            $statusLine = $http_response_header[0] ?? '';
            preg_match('/HTTP\/\S+\s+(\d{3})/', $statusLine, $matches);
            $status = (int)($matches[1] ?? 0);
        }

        return [
            'success' => $status >= 200 && $status < 300,
            'response' => $response,
            'status' => $status,
            'headers' => $http_response_header ?? []
        ];
    }
}