<?php
namespace App\Services;

use Dotenv\Dotenv;
use DateTime;

use App\Models\DeviceModels\DoorSensor;

class DeviceStateService {
    public function getDoorState() {
        $latestActivity = DoorSensor::getLatestActivity();
        $data = json_decode($latestActivity['data'], true);

        if ($data['DOOR_OPEN_STATUS'] == 0) {
            return;
        }

        $openedAt = new DateTime($latestActivity['dateTime']);
        $now = new DateTime();

        $interval = $openedAt->diff($now);

        $minutesOpen = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        if ($minutesOpen >= 10) {
            return 2;
        }
    }
}