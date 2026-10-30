<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;

class IndoorAmbienceMonitoringSensor extends Sensor
{
    public $battery;

    public $co2;

    public $humidity;

    public $light_level;

    public $pm10;

    public $pm2_5;

    public $pressure;

    public $temperature;

    public $tvoc;
}