<?php

use App\Controllers\DashboardController;
use App\Controllers\WebhookController;

// WEB ROUTES
$router->get('/', DashboardController::class, 'index');
$router->get('/dashboard', DashboardController::class, 'index');
$router->get('/dashboard/data', DashboardController::class, 'dashboardData');
$router->get('/api/door-state', DashboardController::class, 'doorState');
$router->post('/api/door-alarm', DashboardController::class, 'doorAlarm');

// API ROUTES
$router->get('/api/status', WebhookController::class, 'status');

$router->post('/api/webhook/ttn', WebhookController::class, 'handle');
