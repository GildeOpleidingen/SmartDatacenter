<?php
require_once(__DIR__ . '/../Services/Definer.php');

class Sensor extends JsonDeserializer
{
    public $device_id;
    public $sensorType;
    public $status = "Good";
    
    function setDeviceId($value){
        $this->device_id = $value;
    }
    function setSensorType($value){
        $this->sensorType = $value;
    }
    function setStatus($value){
        $this->status = $value;
    }
    

}

class DoorSensor extends Sensor
{
    public $ALARM;
    
    public $BAT_V;
    
    public $DOOR_OPEN_STATUS;
    
    public $DOOR_OPEN_TIMES;

    public $LAST_DOOR_OPEN_DURATION;

    public $MOD;
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
    public $Ext;
    
    public $Hum_SHT;
    
    public $Systimestamp;
    
    public $TempC_DS;

    public $TempC_SHT;
}
