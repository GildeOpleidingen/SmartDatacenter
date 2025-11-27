<?php
include_once __DIR__ . '/includes/database/DBParser.php';
include_once __DIR__ . '/includes/Definer.php';
$parser = new DBParser();
$define = new Definer();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #222226; }
        .card-dot { width: 12px; height: 12px; border-radius: 9999px; background: #1E40AF; border: 3px solid white; }
    </style>
</head>
<body class="text-white p-8">

    <!-- Header -->
    <div class="flex items-center mb-10">
        <img src="/src/img/GildeLogoDatacenter.svg" class="h-20">
        <div class="flex-1 ml-6 border-t border-gray-500"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <?php foreach ($device_cards as $card): 
            [$borderColor, $statusLabel] = $define->status_color_and_label($card["status"]);
            $fields = $sensor_fields[$card["type"]] ?? [];
        ?>
        <div class="bg-[#2b2b2d] p-6 rounded-2xl border-2" style="border-color: <?= $borderColor ?>">

            <!-- Title + Status -->
            <div class="flex justify-between">
                <div class="flex items-center">
                    <span class="card-dot mr-2"></span>
                    <span class="text-lg font-semibold"><?= htmlspecialchars($card["device_Id"]) ?></span>
                </div>
                <div class="text-right">
                    <div class="font-semibold"><?= $statusLabel ?></div>
                    <div class="text-gray-400 text-sm"><?= htmlspecialchars($card["type"]) ?></div>
                </div>
            </div>

            <!-- Payload -->
            <div class="mt-4 text-sm">
                <?php if (!$card["payload"]): ?>
                    <div class="text-gray-400">No data available.</div>
                <?php else: ?>
                    <ul class="space-y-1">
                        <?php foreach ($fields as $key):  
                            if (!isset($card["payload"][$key])) continue;
                            $value = $card["payload"][$key];

                            if ($key === "DOOR_OPEN_STATUS") {
                                $value = $define->door_state_label($value);
                            }
                        ?>
                        <li class="flex justify-between">
                            <span class="text-gray-300"><?= $define->getLabels($key) ?></span>
                            <span class="font-medium"><?= htmlspecialchars($value) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Updated -->
            <div class="text-right text-gray-400 text-sm mt-4">
                <?= $define->format_updated($card["last_update"]) ?>
            </div>

        </div>
        <?php endforeach; ?>

    </div>

</body>
</html>