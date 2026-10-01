<?php
namespace App\Controllers;

use App\Services\DBParser;
use App\Services\Definer;

class DashboardController {
    public function index() {
        $parser = new DBParser();
        $definer = new Definer();

        $deviceCards = $parser->generateCards();
        $fields = $definer->getFields();

        header("refresh: 5;");

        require __DIR__ . '/../../views/Dashboard.php';
    }
}
