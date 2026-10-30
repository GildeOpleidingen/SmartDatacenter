<?php
namespace App\Services;

use DateTime;

use App\Models\DeviceModels\DoorSensor;
use App\Models\DeviceModels\Busylight;

use App\Services\DownlinkService;

class DeviceStateService {
    public function getDoorState() {
        $latestActivity = DoorSensor::getLatestActivity();

        if (!$latestActivity || empty($latestActivity['data'])) {
            return 0;
        }

        $data = json_decode($latestActivity['data'], true);

        if (!is_array($data) || !isset($data['DOOR_OPEN_STATUS'])) {
            return 0;
        }

        if ((int) $data['DOOR_OPEN_STATUS'] === 0) {
            return 0;
        }

        $openedAt = new DateTime($latestActivity['dateTime'] ?? 'now');
        $now = new DateTime();
        $interval = $openedAt->diff($now);

        $minutesOpen = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        return ($minutesOpen >= 5) ? 1 : 1;
    }

    public function streamDoorState(DeviceStateService $service) {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        $lastStatus = null;

            while (true) {
            $status = $service->getDoorState();

            if ($status !== $lastStatus) {
                echo "data: " . json_encode(['door_status' => $status]) . "\n\n";
                ob_flush();
                flush();
                $lastStatus = $status;
            }

            sleep(2);
        }
    }

    public function updateBusylight($doorStatus = null) {
        if ($doorStatus === null) {
            $doorStatus = $this->getDoorState();
        }

        error_log("Door status: " . $doorStatus);

        $bl = new Busylight();
        $currentColor = $bl->getCurrentColor();

        error_log("Current color: " . json_encode($currentColor));

        if ($currentColor && $currentColor['state'] == $doorStatus) {
            error_log("Door state unchanged, skipping update");
            return;
        }

        $p = [];
         #Eerste is Rood, tweede is blauw, derde is groen, vierde is aan vijfde is uit.
        switch ((int) $doorStatus) {
            case 2:
                $p = [255, 0, 0, 10, 10];
                error_log("2");
                break;
            case 1:
                $p = [255, 160, 0, 10, 0];
                error_log("1");
                break;
            case 0:
                $p = [0, 0, 255, 10, 0];
                error_log("0");
                break;
            default:
                $p = [0, 255, 0, 10, 0];
                break;
        }

        error_log("Sending downlink with RGB: " . json_encode($p));

        $result = DownlinkService::busylight($p[0], $p[1], $p[2], $p[3], $p[4]);

        error_log("Downlink result: " . json_encode($result));

        if (!$result['success']) {
            error_log("Downlink failed!");
            return;
        }

        $bl->updateCurrentColor($p[0], $p[1], $p[2], (int) $doorStatus);
        return 'success';
    }
} 