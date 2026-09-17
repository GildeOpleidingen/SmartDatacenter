<?php
namespace App\Models\DeviceModels;

use App\Models\Sensor;

class TempSensor extends Sensor
{
    public $Ext;
    
    public $Hum_SHT;
    
    public $Systimestamp;
    
    public $TempC_DS;

    public $TempC_SHT;
}