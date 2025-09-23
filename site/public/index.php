<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datacenter Dashboard</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <h1>Datacenter Status</h1>
    </header>
    <main>
        <div class="sensor-card">
            <h2>Temperatuur</h2>
            <p id="temperature-value" class="sensor-value">-- °C</p>
            <small id="temperature-time">Laatste update: nog niet bekend</small>
        </div>

        <div class="sensor-card">
            <h2>Luchtvochtigheid</h2>
            <p id="humidity-value" class="sensor-value">-- %</p>
            <small id="humidity-time">Laatste update: nog niet bekend</small>
        </div>

        <div class="sensor-card">
            <h2>Deurstatus</h2>
            <p id="door-value" class="sensor-value">--</p>
            <small id="door-time">Laatste update: nog niet bekend</small>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>