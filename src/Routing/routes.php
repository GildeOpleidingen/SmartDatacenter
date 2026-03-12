<?php

use App\Controllers\DashboardController;
use App\Controllers\SettingsController;
use App\Controllers\WebhookController;

$router->get('/', DashboardController::class, 'index');
$router->get('/dashboard', DashboardController::class, 'index');

$router->get('/settings', SettingsController::class, 'index');
$router->post('/settings', SettingsController::class, 'updateSettings');

$router->post('/api/webhooks/ttn', WebhookController::class, 'handle');
