<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\DeviceStateService;

$dss = new DeviceStateService();
$dss->getDoorState();