<?php
namespace App\Services;

use PDO;
use PDOException;
use App\Database\DBConn;

include_once __DIR__ . '/ParseService.php';
include_once __DIR__ . '/../Database/DBConnection.php';


class Definer{
    
    private $pdo;
    public function __construct() {
        $this->pdo = DBConn::getInstance();
    } 

    public function getLabels($key) {
        $map = [
            'current' => 'Current (mA)',
            'factor' => 'Power Factor (%)',
            'power' => 'Power (W)',
            'power_sum' => 'Total Energy (Wh)',
            'state' => 'State',
            'voltage' => 'Voltage (V)',

            'DOOR_OPEN_STATUS' => 'Door State',
            'BAT_V' => 'Battery Voltage (V)',
            'DOOR_OPEN_TIMES' => 'Times Opened',
            'LAST_DOOR_OPEN_DURATION' => 'Last Open Duration (s)',
            'ALARM' => 'Alarm',
            'MOD' => 'Mode',

            'TempC_SHT' => 'Temp SHT (°C)',
            'TempC_DS' => 'Temp DS (°C)',
            'Hum_SHT' => 'Humidity (%)',
            'Ext' => 'External Sensor',
            'Systimestamp' => 'System Timestamp',

            'motion' => 'Motion',
            'battery_volt' => 'Battery Voltage (V)',
            'tamper' => 'Tamper',
            'count' => 'Counter',
            'time' => 'Time',
            'button' => 'Test Button'
        ];
        return $map[$key] ?? ucwords(str_replace('_', ' ', $key));
    }
    public function getFields(): array {
       $sensor_fields = [
        'powerSocket' => ['voltage','current','factor','power','power_sum','state'],
        'doorSensor' => ['DOOR_OPEN_STATUS','BAT_V','DOOR_OPEN_TIMES','LAST_DOOR_OPEN_DURATION','ALARM','MOD'],
        'temperatureSensor' => ['TempC_SHT','Hum_SHT','TempC_DS','Ext','Systimestamp'],
        'motionSensor' => ['motion','battery_volt','tamper','count','time']
    ];
    return $sensor_fields;
    }

    public function door_state_label($v) {
        if ($v == 1) return "Open";
        if ($v == 0) return "Closed";
        return "Unknown";
    }

    public function status_color_and_label($logStatus) {
        switch (strtolower($logStatus)) {
            case 'warning': return ['#ff2b2b', 'Warning'];
            case 'error': return ['#ffd300', 'Error'];
            default: return ['#22c55e', 'Operational'];
        }
    }

    public function format_updated($ts) {
        if (!$ts) return "Never";
        $t1 = strtotime($ts);
        $t2 = time();
        $diff = $t2 - $t1;

        if ($diff < 60) return "Updated $diff seconds ago";
        if ($diff < 3600) return "Updated " . floor($diff/60) . " minutes ago";
        if ($diff < 86400) return "Updated " . floor($diff/3600) . " hours ago";
        return "Updated " . floor($diff/86400) . " days ago";
    }

     public function getDevices(): array{
        $devices = $this->pdo->query("
            SELECT d.deviceID, d.type_ID
            FROM device d
            ORDER BY d.ID
        ")->fetchAll(PDO::FETCH_ASSOC);

        return $devices;
    }

    public function getDeviceType($deviceId) {
        $result = $this->pdo->prepare("
            SELECT d.type_Id, d.type_name a.*
            FROM device d., activity a
            join d on d.Id = a.device_Id
            WHERE device_Id = ?
        ");
        $result->execute([$deviceId]);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getActivity($deviceId) {
        $stmt = $this->pdo->prepare("
        SELECT a.data, a.dateTime
              FROM activity a
              JOIN device d ON a.device_ID = d.ID
              WHERE d.deviceID = :deviceId
              ORDER BY a.dateTime DESC
              LIMIT 1
    ");
        $stmt->bindParam('deviceId', $deviceId);
        if (!$stmt->execute()) {
            throw new PDOException();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLogs(){
        $log = $this->pdo->prepare("
            SELECT status
            FROM log
            WHERE device_Id = ?
            ORDER BY date DESC LIMIT 1
        ");
        return $log->fetch(PDO::FETCH_ASSOC);
    }

    public function getDeviceIcon($sensorType, $status, $DoorState, $motion, $powerState){

        switch($status){
            case 'Warning':
                $path = "/img/sensors/{$sensorType}/" . ucfirst($sensorType) . "Warning.svg";
                break;
            case 'Error':
                $path = "/img/sensors/{$sensorType}/" . ucfirst($sensorType) . "Error.svg";
                break;
            case 'Good':
            default:
                switch ($sensorType) {
                    case 'powerSocket':
                        $path = ($powerState == 'open') ? '/img/sensors/powerSocket/PowerSocketOn.svg' : '/img/sensors/powerSocket/PowerSocketOff.svg';
                        break;
                    case 'temperatureSensor':
                        $path = '/img/sensors/temperatureSensor/TemperatureGood.svg';
                        break;
                    case 'motionSensor':
                        $path = ($motion == 1) ? '/img/sensors/motionSensor/MotionSensorDetected.svg' : '/img/sensors/motionSensor/MotionSensorIdle.svg';
                        break;
                    case 'doorSensor':
                        $path = ($DoorState == 1) ? '/img/sensors/doorSensoropenicon.svg' : '/img/sensors/doorSensorclosedicon.svg';
                        break;
                }
                break;
        }
        return "<img src=\"$path\" width=\"64px\" class=\"ml-auto\">";
    }

}





?>