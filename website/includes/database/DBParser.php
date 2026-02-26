<?php
include_once __DIR__ . '/DBConnection.php';
include_once __DIR__ . '/../Definer.php';
require_once(__DIR__ . '/../Models/Devices.php');

class DBParser {
    public $definer;
    private $pdo;

    public function __construct() {
        $this->pdo = DBConn::getInstance();
        $this->definer = new Definer();
    }

    public function parseDataToDB($json) {
        $stmt = $this->pdo->prepare("
            INSERT INTO activity (data, date)
            VALUES (?, NOW())
        ");
       $stmt->bindParam(1, $json);
       $stmt->execute();
    }
    
    public function generateCards() {
        $devices = $this->definer->getDevices();
        $card_info = [];
        foreach ($devices as $key => $dev) {
            if ($dev["device_Id"]){
                $activity = $this->definer->getActivity([$dev["device_Id"]]);
                if ($activity && trim($activity["data"]) !== "") {
                    $card_info[$key]= $this->getSensorType($dev["type_Id"], json_decode($activity["data"]));
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
