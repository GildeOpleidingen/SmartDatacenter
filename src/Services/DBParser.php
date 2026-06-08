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

                        // Roept de dynamische statusbepaling aan
                        $card->status = $this->determineStatus($card);

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

    /**
     * Bepaalt de status van de sensor.
     * Retouneert exact 'Warning', 'Error' of 'Operational' zodat de SVG-bestanden matchen.
     */
    private function determineStatus($card) {
        // Standaard status (Groen en Operational)
        $status = "Operational";

        switch($card->sensorType) {
            case "temperatureSensor":
                // Pas hier de temperatuur drempelwaarden aan
                $warningTemp = 28;
                $errorTemp = 30;

                // Haal de waarde op uit TempC_SHT (of TempC_DS indien gewenst)
                $currentTemp = $card->TempC_SHT ?? 0;

                if ($currentTemp >= $errorTemp) {
                    $status = "Warning"; // Geeft een Rode border en pakt TemperatureSensorWarning.svg
                } elseif ($currentTemp >= $warningTemp) {
                    $status = "Error";   // Geeft een Oranje border en pakt TemperatureSensorError.svg
                }
                break;

            case "doorSensor":
                // Pas hier het maximaal aantal seconden aan dat een deur open mag staan
                $maxOpenSeconds = 30;

                if (isset($card->DOOR_OPEN_STATUS) && $card->DOOR_OPEN_STATUS == 1) {
                    $status = "Error"; // Deur open (Oranje border)

                    if (isset($card->LAST_DOOR_OPEN_DURATION) && $card->LAST_DOOR_OPEN_DURATION >= $maxOpenSeconds) {
                        $status = "Warning"; // Te lang open (Rode border)
                    }
                }
                break;

            case "motionSensor":
                // Als er beweging gedetecteerd is (1), stuur status naar Oranje
                if (isset($card->motion) && $card->motion == 1) {
                    $status = "Error"; // Pakt MotionSensorError.svg en geeft oranje border
                }
                break;

            case "powerSocket":
                // Pas hier het basis-wattage en de percentages aan (1.10 = +10%, 1.15 = +15%)
                $basePower = 100;
                $warningThreshold = 1.10;
                $errorThreshold = 1.15;

                if (isset($card->power)) {
                    $percentage = $card->power / $basePower;
                    if ($percentage >= $errorThreshold) {
                        $status = "Warning"; // Rood
                    } elseif ($percentage >= $warningThreshold) {
                        $status = "Error";   // Oranje
                    }
                }
                break;
        }

        return $status;
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