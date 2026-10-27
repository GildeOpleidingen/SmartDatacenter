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
        <section style="padding: 120px; align-items: center;">
            <div class="container max-w-screen-xl">
              <!--  <div class="grid grid-cols-8 md:grid-cols-2 gap-10 max-w-fit"> -->
                <div class="inline-grid max-lg:grid-cols-3 lg:grid-cols-4 md:grid-cols-2 gap-10">
                    <?php
                    foreach ($deviceCards as $card):
                        [$borderColor, $statusLabel] = $definer->status_color_and_label($card->status);  /* Will implement status later */
                        ?>
                        <div class="flex flex-col justify-between items-center rounded-xl border-2 overflow-hidden p-8 h-full" style="border-color: <?= $borderColor ?>"> <!-- card -->
                            <div class="<?= in_array($card->sensorType, ['soundLevelSensor', 'doorSensor'], true) ? 'w-32' : 'w-11' ?> block h-auto">
                                <?php
                                //echo $card->DOOR_OPEN_STATUS;
                                echo $deviceIcon = $definer->getDeviceIcon($card->sensorType, $card->status, isset($card->DOOR_OPEN_STATUS) ? $card->DOOR_OPEN_STATUS : null, isset($card->motion) ? $card->motion : null, isset($card->state) ? $card->state : null) ?? "<img src='/img/sensors/default/GildeDataCenterIconGood.svg' width=\"64px\" class=\"ml-auto\">";
                                ?>
                            </div>
                            <div class="flex-grow"></div>
                            <ul class="flex flex-col items-center justify-center bg-[#001] overflow-hidden w-full"> <!-- card-inner -->
                                    <p class="text-lg font-semibold"><?= htmlspecialchars(match (strtolower((string) $card->device_id)) {
                                        'soundlevelsensor-001' => 'geluid-level',
                                        'deursensor-002' => 'Voordeur',
                                        'deursensor-003' => 'Achterdeur',
                                        'motion-001' => 'Beweging',
                                        'temphumidity-001' => 'Temperatuur',
                                        default => $card->device_id,
                                    }) ?></p>
                                    <?php foreach ($card as $key => $value):
                                        if ($key === "device_id" || $key === "sensorType" || $key === "button" || $key === "MOD" || $key === "temperature" || $key === "humi" || $key === "status") continue;
                                        ?>
                                        <li class="grid grid-cols-2 gap-1 w-full">
                                            <span class="text-gray-300 text-right break-all text-xs"><?= $definer->getLabels($key) ?></span>
                                            <span class="font-medium break-all  text-xs">= <?= htmlspecialchars($value) ?></span>
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