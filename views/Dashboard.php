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
                                <!-- <div class="text-right">
                                    div class="font-semibold"><//?= $statusLabel ?></div
                                </div> -->
                            <div class="w-11 block h-auto">
                                <?php
                                //echo $card->DOOR_OPEN_STATUS;
                                echo $deviceIcon = $definer->getDeviceIcon($card->sensorType, $card->status, isset($card->DOOR_OPEN_STATUS) ? $card->DOOR_OPEN_STATUS : null, isset($card->motion) ? $card->motion : null, isset($card->state) ? $card->state : null) ?? "<img src='/img/sensors/default/GildeDataCenterIconGood.svg' width=\"64px\" class=\"ml-auto\">";
                                ?>
                            </div>
                            <div class="flex-grow"></div>
                            <ul class="flex flex-col items-center justify-center bg-[#001] overflow-hidden w-full"> <!-- card-inner -->

                                    <p class="text-lg font-semibold"><?= htmlspecialchars($card->device_id) ?></p>
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
</body>
</html>