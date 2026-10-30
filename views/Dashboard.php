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
                <div id="dashboard-data" class="grid w-full grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <?php require __DIR__ . '/DashboardCards.php'; ?>
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
let dashboardRefreshInProgress = false;

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
  setInterval(refreshDashboardData, 2000);
});

async function refreshDashboardData() {
  if (dashboardRefreshInProgress) {
    return;
  }

  dashboardRefreshInProgress = true;
  try {
    const response = await fetch(`/dashboard/data?_=${Date.now()}`, { cache: 'no-store' });
    if (!response.ok) {
      throw new Error(`Dashboard refresh failed with status ${response.status}`);
    }
    document.getElementById('dashboard-data').innerHTML = await response.text();
  } catch (error) {
    console.error('Failed to refresh dashboard data:', error);
  } finally {
    dashboardRefreshInProgress = false;
  }
}
</script>
</body>
</html>