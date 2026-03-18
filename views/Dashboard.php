<?php
include_once __DIR__ . '/../src/Services/DBParser.php';
include_once __DIR__ . '/../src/Services/Definer.php';
$parser = new DBParser();
$define = new Definer();
$deviceCards = $parser->generateCards();
$fields = $define->getFields();
header("refresh: 5;");
?>
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
    <div class="main_navigation"></div>
    <div class="main_wrapper">
        <section>
            <div class="container">
                <!-- Header -->
                <div class="flex flex-col mb-10">
                    <img src="/img/GildeLogoDatacenter.svg" width="250px" class="mb-7">
                    <div class="flex-1 ml-6 border-t border-gray-500"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <?php foreach ($deviceCards as $card):
                        [$borderColor, $statusLabel] = $define->status_color_and_label($card->status);  /* Will implement status later */
                        ?>
                        <div class="bg-[#001] p-6 rounded-2xl border-2" style="border-color: <?= $borderColor ?>">
                            <div class="flex justify-between">
                                <div class="flex items-center">
                                    <span class="card-dot mr-2"></span>
                                    <span class="text-lg font-semibold"><?= htmlspecialchars($card->device_id) ?></span>
                                </div>
                                <div class="text-right">
                                    <!--div class="font-semibold"><//?= $statusLabel ?></div-->
                                </div>
                            </div>

                            <div class="mt-4 text-sm flex">
                                <ul class="space-y-1">
                                    <?php foreach ($card as $key => $value):
                                        if ($key === "device_id" || $key === "sensorType" || $key === "button" || $key === "MOD" || $key === "temperature" || $key === "humi" || $key === "status") continue;
                                        ?>
                                        <li class="grid grid-cols-2 gap-1">
                                            <span class="text-gray-300 text-right"><?= $define->getLabels($key) ?></span>
                                            <span class="font-medium">= <?= htmlspecialchars($value) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php
                                //echo $card->DOOR_OPEN_STATUS;
                                echo $deviceIcon = $define->getDeviceIcon($card->sensorType, $card->status, isset($card->DOOR_OPEN_STATUS) ? $card->DOOR_OPEN_STATUS : null, isset($card->motion) ? $card->motion : null, isset($card->state) ? $card->state : null);
                                ?>

                            </div>

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
</body>
</html>