<?php
namespace App\Services;

use DateTime;

use App\Models\DeviceModels\DoorSensor;
use App\Models\DeviceModels\Busylight;

use App\Services\DownlinkService;

class DeviceStateService {
    public function getDoorState() {
        $latestActivity = DoorSensor::getLatestActivity();
        $data = json_decode($latestActivity['data'], true);

        if ($data['DOOR_OPEN_STATUS'] == 0) {
            return 0;
        }

        $openedAt = new DateTime($latestActivity['dateTime']);
        $now = new DateTime();

        $interval = $openedAt->diff($now);

        $minutesOpen = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        if ($minutesOpen >= 5) {
            return 2;
        } else {
            return 1;
        }
    }

    public function updateBusylight() {
        $doorStatus = $this->getDoorState();
        
        $bl = new Busylight();
        $currentColor = $bl->getCurrentColor();

        if ($currentColor['state'] == $doorStatus) {
            return;
        }

        $p = [];
        switch ($doorStatus) {
            case '2':
                $p = [255, 0, 0, 10, 10];
                break;
            case '1':
                $p = [255, 160, 0, 10, 0];
                break;
            case '0':
            default:
                $p = [0, 255, 0, 10, 0];
                break;
            }

        $result = DownlinkService::busylight($p[0], $p[1], $p[2], $p[3], $p[4]);

        if (!$result['success']) {
            return;
        }

        $bl->updateCurrentColor($p[0], $p[1], $p[2], $doorStatus);
        return 'success';
    }
}