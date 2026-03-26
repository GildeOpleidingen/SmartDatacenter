<?php
namespace App\Services;

use App\Database\DBConn;
use PDO;
use App\Models\DoorSensor;
use App\Models\TempSensor;
use App\Models\MotionSensor;
use App\Models\PowerSocket;
include_once __DIR__ . '/../Database/DBConnection.php';
include_once __DIR__ . '/Definer.php';
require_once(__DIR__ . '/../Models/DeviceModels.php');

class DBParser {
    public $definer;
    private $pdo;

    public function __construct() {
        $this->pdo = DBConn::getInstance();
        $this->definer = new Definer();
    }

    public function parseDataToDB($json) {
        $stmt = $this->pdo->prepare("
            INSERT INTO activity (device_ID, data, dateTime)
            VALUES (:device_id, :data, NOW())
        ");
        $num = 1;
       $stmt->bindParam('device_id', $num, PDO::PARAM_INT);
       $stmt->bindParam('data', $json, PDO::PARAM_STR);
       $stmt->execute();
    }
    
    public function generateCards() {
        $devices = $this->definer->getDevices();
        $card_info = [];
        foreach ($devices as $key => $dev) {
            if ($dev["deviceID"]){
                $activity = $this->definer->getActivity([$dev["deviceID"]]);
                if ($activity && trim($activity["data"]) !== "") {
                    $card_info[$key]= $this->getSensorType($dev["type_ID"], json_decode($activity["data"]));
                }
            }
        }
        return $card_info;
    }
                
    

    public function getSensorType($type, $payload) {
        $abstractClass = null;    
        switch($type){
            case "doorSensor":
                $abstractClass = DoorSensor::Deserialize(json_encode($payload->uplink_message->decoded_payload));
                $abstractClass->setDeviceId($payload->end_device_ids->device_id);
                $abstractClass->setSensorType($type);
                break;
            case "temperatureSensor":
                $abstractClass = TempSensor::Deserialize(json_encode($payload->uplink_message->decoded_payload));
                $abstractClass->setDeviceId($payload->end_device_ids->device_id);
                $abstractClass->setSensorType($type);
                break;
            case "motionSensor":
                $abstractClass = MotionSensor::Deserialize(json_encode($payload->uplink_message->decoded_payload));
                $abstractClass->setDeviceId($payload->end_device_ids->device_id);
                $abstractClass->setSensorType($type);
                break;
            case "powerSocket":
                $abstractClass = PowerSocket::Deserialize(json_encode($payload->uplink_message->decoded_payload));
                $abstractClass->setDeviceId($payload->end_device_ids->device_id);
                $abstractClass->setSensorType($type); 
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
