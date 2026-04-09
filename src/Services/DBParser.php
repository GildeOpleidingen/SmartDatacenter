<?php
namespace App\Services;

use App\Database\DBConn;
use App\Services\Definer;
use PDO;
use App\Models\Sensor;
use App\Models\DeviceModels\DoorSensor;
use App\Models\DeviceModels\TempSensor;
use App\Models\DeviceModels\MotionSensor;
use App\Models\DeviceModels\PowerSocket;
use App\Models\DeviceModels\SoundLevelSensor;
use App\Models\DeviceModels\IndoorAmbienceMonitoringSensor;

class DBParser {
    public $definer;
    private $pdo;

    public function __construct() {
        $this->pdo = DBConn::getInstance();
        $this->definer = new Definer();
    }

    public function parseDataToDB($ttn_id, $json) {
        if (empty($json) || !$json) return;
        
        $payload = json_encode($json);

        $id = $this->getOrCreateDevice($ttn_id);

        $stmt = $this->pdo->prepare("
            INSERT INTO activity (device_ID, data, dateTime)
            VALUES (:device_id, :data, NOW())
        ");
       $stmt->bindParam('device_id', $id, PDO::PARAM_INT);
       $stmt->bindParam('data', $payload, PDO::PARAM_STR);
       $stmt->execute();
    }
    
    public function generateCards()
    {
        $devices = $this->definer->getDevices();
        $card_info = [];

        if (!is_array($devices)) return [];
        foreach ($devices as $key => $dev) {

            if ($dev["deviceID"]) {
                $activity = $this->definer->getActivity($dev["deviceID"]);
                if (is_array($activity) && trim($activity["data"]) != "") {
                    $card = $this->getSensorType($dev["deviceID"], json_decode($activity["data"]));

                    if ($card) {
                        $card->device_id = $dev["deviceID"];
                        $card->status = "goed";
                        array_push($card_info, $card);
                    }
                }
            }
        }
        return $card_info;
    }

    private function getOrCreateDevice($ttn_id) {
        $stmt = $this->pdo->prepare("SELECT id FROM device WHERE deviceID = :name LIMIT 1");
        $stmt->execute([':name' => $ttn_id]);
        $device = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($device) {
            return (int) $device['id'];
        }

        $insertStmt = $this->pdo->prepare("INSERT INTO device (deviceID) VALUES (:name)");
        $insertStmt->execute([':name' => $ttn_id]);

        return (int) $this->pdo->lastInsertId();
    }  

    public function getSensorType($deviceId, $payload) {
        $deviceArray = explode("-", $deviceId);
        $type = strtolower($deviceArray[0]);

        $abstractClass = null;
        switch($type){
            case "deursensor":
                $abstractClass = DoorSensor::Deserialize(json_encode($payload));
                $abstractClass->setSensorType("doorSensor");
                break;
            case "temphumidity": // Temperatuur
                $abstractClass = TempSensor::Deserialize(json_encode($payload));
                $abstractClass->setSensorType("temperatureSensor");
                break;
            case "motion":
                $abstractClass = MotionSensor::Deserialize(json_encode($payload));
                $abstractClass->setSensorType("motionSensor");
                break;
            case "powersocket": // Powersocket
                $abstractClass = PowerSocket::Deserialize(json_encode($payload));
                $abstractClass->setSensorType("powerSocket");
                break;
            case "soundlevelsensor":
                $abstractClass = SoundLevelSensor::Deserialize(json_encode($payload));
                $abstractClass->setSensorType("soundLevelSensor");
                break;
            case "indoorambiencemonitoringsensor":
                $abstractClass = IndoorAmbienceMonitoringSensor::Deserialize(json_encode($payload));
                $abstractClass->setSensorType("indoorAmbienceMonitoringSensor");
                break;
            }

            return $abstractClass;

        }
        // log
        // $getLog->execute([$dev["device_Id"]]);
        // $log = $getLog->fetch();
        // $status = $log["status"] ?? "Good";

        // $device_cards[] = [
        //     "device_Id" => $dev["device_Id"],
        //     "type" => $dev["type_Id"],
        //     "payload" => $payload,
        //     "last_update" => $activity["date"] ?? null,
        //     "status" => $status
        // ];
    
}
