<?php
$customerVisibleFields = [
    'powersocket-001' => [],
    'temphumidity-001' => ['TempC_DS', 'TempC_SHT'],
    'soundlevelsensor-001' => ['la', 'lamax'],
    'indoorambiencemonitoringsensor' => ['co2', 'humidity'],
    'motion-001' => ['motion', 'count'],
    'deursensor-002' => ['DOOR_OPEN_STATUS', 'DOOR_OPEN_TIMES', 'LAST_DOOR_OPEN_DURATION'],
    'deursensor-003' => ['DOOR_OPEN_STATUS', 'DOOR_OPEN_TIMES', 'LAST_DOOR_OPEN_DURATION'],
];

foreach ($deviceCards as $card):
    [$borderColor, $statusLabel] = $definer->status_color_and_label($card->status);
    $doorState = $card->DOOR_OPEN_STATUS ?? null;
    if ($card->sensorType === 'doorSensor' && in_array((string) $doorState, ['0', '1', '2'], true)) {
        $borderColor = match ((string) $doorState) {
            '0' => '#22c55e',
            '1' => '#ff8800',
            '2' => '#ff2b2b',
        };
    }
    $visibleFields = $customerVisibleFields[strtolower((string) $card->device_id)] ?? [];
    ?>
    <div class="flex min-w-0 flex-col justify-between items-center rounded-xl border-2 overflow-hidden p-8 h-full" style="border-color: <?= $borderColor ?>">
        <div class="<?= in_array($card->sensorType, ['soundLevelSensor', 'doorSensor', 'indoorAmbienceMonitoringSensor'], true) ? 'w-32' : 'w-11' ?> block max-w-full h-auto">
            <?= $definer->getDeviceIcon($card->sensorType, $card->status, isset($card->DOOR_OPEN_STATUS) ? $card->DOOR_OPEN_STATUS : null, isset($card->motion) ? $card->motion : null, isset($card->state) ? $card->state : null, isset($card->la) ? $card->la : null) ?? "<img src='/img/sensors/default/GildeDataCenterIconGood.svg' width=\"64\" class=\"block max-w-full h-auto ml-auto\">" ?>
        </div>
        <div class="flex-grow"></div>
        <ul class="flex flex-col items-center justify-center bg-[#001] overflow-hidden w-full">
            <p class="text-lg font-semibold"><?= htmlspecialchars(match (strtolower((string) $card->device_id)) {
                'powersocket-001' => 'Stopcontact',
                'soundlevelsensor-001' => 'Geluid-Level',
                'deursensor-002' => 'Voordeur',
                'deursensor-003' => 'Achterdeur',
                'motion-001' => 'Beweging',
                'temphumidity-001' => 'Temperatuur',
                'indoorambiencemonitoringsensor' => 'Luchtkwaliteit',
                default => $card->device_id,
            }) ?></p>
            <?php foreach ($card as $key => $value):
                if (!in_array($key, $visibleFields, true)) continue;
                ?>
                <li class="grid grid-cols-2 gap-1 w-full">
                    <span class="text-gray-300 text-right break-all text-xs"><?= htmlspecialchars($definer->getLabels($key)) ?></span>
                    <span class="font-medium break-all text-xs">= <?= htmlspecialchars((string) $value) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="text-right text-gray-400 text-sm mt-4"></div>
    </div>
<?php endforeach; ?>
