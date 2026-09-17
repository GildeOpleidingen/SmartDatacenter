<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;

class SoundLevelSensor extends Sensor {

    public $battery;

    public $la;

    public $laeq;

    public $lamax;

    public $freq_weight;

    public $time_weight;
}