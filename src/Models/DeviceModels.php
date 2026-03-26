<?php
namespace App\Models;

abstract class JsonDeserializer
{
    public static function Deserialize($json)
    {
        $className = get_called_class();
        $classInstance = new $className();
        if (is_string($json))
            $json = json_decode($json);
        foreach ($json as $key => $value)
            $classInstance->{$key} = $value;
        return $classInstance;
    }

    public static function DeserializeArray($json)
    {
        $json = json_decode($json);
        $items = [];
        foreach ($json as $item)
            $items[] = self::Deserialize($item);
        return $items;
    }
}

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
