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

namespace App\Models;

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
