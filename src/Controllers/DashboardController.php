<?php
namespace App\Controllers;

class DashboardController {
    public function index() {
        require __DIR__ . '/../../views/Dashboard.php';
    }
}