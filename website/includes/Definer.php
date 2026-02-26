<?php
include_once __DIR__ . '/database/DBParser.php';
include_once __DIR__ . '/database/DBConnection.php';


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
            SELECT d.device_Id, d.type_Id
            FROM device d
            ORDER BY d.Id
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
            SELECT d.device_Id, a.data, a.date, s.type
            FROM device d
            left join sensortype s on s.type = d.type_Id 
            left join activity a on a.device_Id = d.device_Id
            WHERE d.device_Id = ?
            AND a.data like '%decoded_payload%'
            ORDER BY a.date DESC;
        ");
        $stmt->execute($deviceId);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLogs(){
        $log = $this->pdo->prepare("
            SELECT status
            FROM log
            WHERE device_Id = ?
            ORDER BY date DESC LIMIT 1
        ");
    }

    public function getDeviceIcon($sensorType, $status, $DoorState, $motion, $powerState){
        //$sensorType = "beaver"; // test placeholder image for new devices
        switch($status){
            case 'Warning':    
                switch ($sensorType) {
                    case 'doorSensor':
                        $path = 'src/img/sensors/sensors/doorSensor/DoorSensorWarning.svg';
                        break;
                    case 'temperatureSensor':
                        $path = 'src/img/sensors/temperatureSensor/TemperatureWarning.svg';
                        break;
                    case 'motionSensor':
                        $path = 'src/img/sensors/motionSensor/MotionSensorWarning.svg';
                        break;
                    case 'powerSocket':
                        $path = 'src/img/sensors/powerSocket/PowerSocketWarning.svg';
                        break;
                    default:
                        $path = 'src/img/sensors/default/GildeDataCenterIconWarning.svg';
                        break;
                }
                break;
            case 'Error':
                switch ($sensorType) {
                    case 'doorSensor':
                        $path = 'src/img/sensors/doorSensor/DoorSensorError.svg';
                        break;
                    case 'temperatureSensor':
                        $path = 'src/img/sensors/temperatureSensor/TemperatureError.svg';
                        break;
                    case 'motionSensor':
                        $path = 'src/img/sensors/motionSensor/MotionSensorError.svg';
                        break;
                    case 'powerSocket':
                        $path = 'src/img/sensors/powerSocket/PowerSocketError.svg';
                        break;
                    default:
                        $path = 'src/img/sensors/default/GildeDataCenterIconError.svg';
                        break;
                }
                break;
            case 'Good':
                switch ($sensorType) {
                    case 'doorSensor':
                        switch ($DoorState) {
                            case 1:
                                $path = 'src/img/sensors/doorsensoropenicon.svg';
                                break;
                            case 0:
                                $path = 'src/img/sensors/doorsensorclosedicon.svg';
                                break;
                        }
                        break;
                    case 'temperatureSensor':
                        $path = 'src/img/sensors/temperatureSensor/TemperatureGood.svg';
                        break;
                    case 'motionSensor':
                        switch ($motion) {
                            case 1:
                                $path = 'src/img/sensors/motionSensor/MotionSensorDetected.svg';
                                break;
                            case 0:
                                $path = 'src/img/sensors/motionSensor/MotionSensorIdle.svg';
                                break;
                        }
                        break;
                    case 'powerSocket':
                        switch ($powerState) {
                            case 'open':
                                $path = 'src/img/sensors/powerSocket/PowerSocketOn.svg';
                                break;
                            case 'close':
                                $path = 'src/img/sensors/powerSocket/PowerSocketOff.svg';
                                break;
                        }
                        break;
                    default:
                        $path = 'src/img/sensors/default/GildeDataCenterIconGood.svg';
                        break;
                }
        }
        return "<img src=\"$path\" width=\"64px\" class=\"ml-auto\">";
    }


}


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


?>