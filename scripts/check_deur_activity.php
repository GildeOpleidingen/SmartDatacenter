<?php
require __DIR__ . '/../vendor/autoload.php';
use App\Database\DBConn;

$db = DBConn::getInstance();
$stmt = $db->prepare("SELECT a.id, a.device_ID, d.deviceID, a.dateTime, a.data FROM activity a JOIN device d ON a.device_ID = d.ID WHERE d.deviceID LIKE 'deursensor-%' ORDER BY a.dateTime DESC LIMIT 10");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "id=" . $r['id'] . " device_ID=" . $r['device_ID'] . " deviceID=" . $r['deviceID'] . " dateTime=" . $r['dateTime'] . "\n";
    echo $r['data'] . "\n\n";
}

?>
