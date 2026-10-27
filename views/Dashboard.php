<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #001; }
        .card-dot { width: 12px; height: 12px; border-radius: 9999px; background: #1E40AF; border: 3px solid white; }
    </style>
    <link rel="icon" href="/img/favicon.svg" sizes="any" type="image/svg+xml">
</head>
<body class="text-white p-10">
<div class="body_wrapper">
    <div class="main_navigation">
        <section style="padding: 1rem 1.5rem 1rem 1.5rem;  align-items: center;">
                <div class="container">
                     <!-- Header -->
                     <div class="flex flex-col mb-10">
                     </div><img src="/img/GildeLogoDatacenter.svg" width="250px" class="mb-7">
                     <div class="flex-1 ml-6 border-t border-gray-500"></div>
                </div>
            </section>
    </div>
    <div class="main_wrapper">
        <section class="px-4 py-8 sm:px-8 lg:px-12">
            <div class="container mx-auto w-full max-w-screen-xl">
                <div class="grid w-full grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
                        [$borderColor, $statusLabel] = $definer->status_color_and_label($card->status);  /* Will implement status later */
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
                        <div class="flex min-w-0 flex-col justify-between items-center rounded-xl border-2 overflow-hidden p-8 h-full" style="border-color: <?= $borderColor ?>"> <!-- card -->
                                <!-- <div class="text-right">
                                    div class="font-semibold"><//?= $statusLabel ?></div
                                </div> -->
                            <div class="<?= in_array($card->sensorType, ['soundLevelSensor', 'doorSensor', 'indoorAmbienceMonitoringSensor'], true) ? 'w-32' : 'w-11' ?> block max-w-full h-auto">
                                <?php
                                //echo $card->DOOR_OPEN_STATUS;
                                echo $deviceIcon = $definer->getDeviceIcon($card->sensorType, $card->status, isset($card->DOOR_OPEN_STATUS) ? $card->DOOR_OPEN_STATUS : null, isset($card->motion) ? $card->motion : null, isset($card->state) ? $card->state : null, isset($card->la) ? $card->la : null) ?? "<img src='/img/sensors/default/GildeDataCenterIconGood.svg' width=\"64\" class=\"block max-w-full h-auto ml-auto\">";
                                ?>
                            </div>
                            <div class="flex-grow"></div>
                            <ul class="flex flex-col items-center justify-center bg-[#001] overflow-hidden w-full"> <!-- card-inner -->

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

                            <!-- Updated -->
                            <div class="text-right text-gray-400 text-sm mt-4">
                                <!--?= //$define->format_updated($card["last_update"]) ?-->
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>
    </div>
    <div class="main_footer"></div>
</div>

<script>
let doorStatus = <?= (int) $initialDoorState ?>;
let timeoutId = null;
let alarmActive = false;

async function pushDoorAlarmState(state) {
  try {
    await fetch('/api/door-alarm', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ door_status: state })
    });
  } catch (error) {
    console.error('Failed to push door alarm state:', error);
  }
}

function Alarm() {
  if (doorStatus === 1) {
    doorStatus = 2;
    alarmActive = true;
    console.log('Alarm triggered');
    pushDoorAlarmState(2);
    return true;
  }

  return false;
}

function startAlarmTimer() {
  clearTimeout(timeoutId);
  timeoutId = setTimeout(() => {
    Alarm();
  }, 10000);
  console.log('Alarm timer started');
}

function TimerStop() {
  clearTimeout(timeoutId);
  timeoutId = null;
  alarmActive = false;
  console.log('Alarm timer stopped');
}

function setDoorStatus(newStatus) {
  const nextStatus = Number(newStatus);

  if (nextStatus === 0) {
    doorStatus = 0;
    TimerStop();
    return;
  }

  if (nextStatus === 1) {
    if (doorStatus === 2) {
      return;
    }

    if (doorStatus !== 1) {
      doorStatus = 1;
      startAlarmTimer();
    }
    return;
  }

  if (nextStatus === 2) {
    doorStatus = 2;
    alarmActive = true;
  }
}

async function refreshDoorState() {
  try {
    const response = await fetch('/api/door-state');
    const data = await response.json();
    const nextStatus = Number(data.door_status);

    if (nextStatus === 0) {
      if (doorStatus !== 0) {
        setDoorStatus(0);
      }
      return;
    }

    if (nextStatus === 1 && doorStatus !== 2) {
      setDoorStatus(1);
      return;
    }

    if (nextStatus === 1 && doorStatus === 2) {
      console.log('Alarm state already active; ignoring repeated open state.');
    }
  } catch (error) {
    console.error('Failed to fetch door state:', error);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  setDoorStatus(doorStatus);
  setInterval(refreshDoorState, 20000);
});
</script>
</body>
</html>