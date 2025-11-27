<?php
include_once __DIR__ . '/DBConnection.php';
include_once __DIR__ . '/../Definer.php';
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
        $device_cards = [];
        $devices = $this->definer->getDevices();
        foreach ($devices as $dev) {

            // activity
            $this->definer->getActivity()->execute([$dev["device_Id"]]);
            $activity = $this->definer->getActivity()->fetch();

            $payload = null;
            if ($activity && trim($activity["data"]) !== "") {
                $json = json_decode($activity["data"], true);
                if ($json && isset($json["uplink_message"]["decoded_payload"])) {
                    $payload = $json["uplink_message"]["decoded_payload"];
                }
            }
        }
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
