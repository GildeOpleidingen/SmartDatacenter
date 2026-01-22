<?php
require_once(__DIR__ . '/../Definer.php');

class Sensor extends JsonDeserializer
{
    // Base class for sensors
    public $device_id;
    function setValue($value){
        $this->device_id = $value;
    }

}

class DoorSensor extends Sensor
{
    public $alarm;
    
    public $bat_v;
    
    public $door_open_status;
    
    public $door_open_times;

    public $last_door_open_duration;

    public $mod;
}

class MotionSensor extends Sensor
{
    public $battery_volt;
    
    public $button;
    
    public $count;
    
    public $motion;

    public $tamper;

    public $time;
}

class PowerSocket extends Sensor
{
    public $current;
    
    public $factor;
    
    public $power;
    
    public $power_sum;

    public $state;

    public $voltage;
}


class TempSensor extends Sensor
{
    public $ext;
    
    public $hum_sht;
    
    public $systimestamp;
    
    public $tempc_ds;

    public $tempc_sht;
}
