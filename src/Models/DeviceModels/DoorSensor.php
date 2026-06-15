<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;
use App\Database\DBConn;
use PDO;

class DoorSensor extends Sensor
{
    public $ALARM;
    
    public $BAT_V;
    
    public $DOOR_OPEN_STATUS;
    
    public $DOOR_OPEN_TIMES;

    public $LAST_DOOR_OPEN_DURATION;

    public $MOD;

    public static function getLatestActivity() {
        $db = DBConn::getInstance();
        
        $sql = "SELECT * FROM activity WHERE device_ID = 5 LIMIT 1;";
        
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}