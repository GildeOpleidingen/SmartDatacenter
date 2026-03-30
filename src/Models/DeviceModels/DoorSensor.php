<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;

class DoorSensor extends Sensor
{
    public $ALARM;
    
    public $BAT_V;
    
    public $DOOR_OPEN_STATUS;
    
    public $DOOR_OPEN_TIMES;

    public $LAST_DOOR_OPEN_DURATION;

    public $MOD;
}