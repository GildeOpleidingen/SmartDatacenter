<?php

use App\Controllers\DashboardController;
use App\Controllers\WebhookController;

// WEB ROUTES
$router->get('/', DashboardController::class, 'index');
$router->get('/dashboard', DashboardController::class, 'index');

// API ROUTES
$router->get('/api/status', WebhookController::class, 'status');

$router->post('/api/webhook/ttn', WebhookController::class, 'handle');

$router->post('/api/downlink/light', WebhookController::class, 'busylight');
