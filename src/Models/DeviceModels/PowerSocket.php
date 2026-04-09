<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;

class PowerSocket extends Sensor
{
    public $current;
    
    public $factor;
    
    public $power;
    
    public $power_sum;

    public $state;

    public $voltage;
}