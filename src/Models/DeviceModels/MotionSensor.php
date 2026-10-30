<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;

class MotionSensor extends Sensor
{
    public $battery_volt;
    
    public $button;
    
    public $count;
    
    public $motion;

    public $tamper;

    public $time;

    public $humi;

    public $temperature;
}