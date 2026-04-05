<?php
include_once 'ui_mode.php';

$mappingfiles = is_dir('/etc/openjvs/games') ? scandir('/etc/openjvs/games') : [];

$path  = '/boot/roms/';
$_scan = is_dir($path) ? scandir($path) : [];
$files = array_values(array_diff($_scan, ['.', '..']));

$f = fopen("csv/romsinfo.csv", "r");
fgetcsv($f); // skip header

$rows = [];
while (($row = fgetcsv($f)) !== false) {
    if (in_array($row[1], $files)) {
        $rows[] = $row;
    }
}
fclose($f);


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Edit JVS Mappings</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('gamepad') . ' Update Game Mappings</h1>';
    echo '<p class="page-intro">Assign OpenJVS mapping files to each game.</p>';

    echo '<div class="card">';
    echo '<div class="card-body" style="padding:0;">';
    echo '<table class="table" style="width:100%"><thead><tr><th>Game Name</th><th>Control Type</th><th>Current Mapping</th><th>New Mapping</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row[4], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row[11], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row[14], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td><form method="POST" action="updatecsvmapping.php" style="display:flex;gap:6px;align-items:center;">';
        echo '<select name="mapping" class="form-select form-select--compact">';
        for ($i = 2; $i < count($mappingfiles); $i++) {
            $mf    = $mappingfiles[$i];
            $value = $row[1] . '#' . $mf;
            echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($mf, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select><input type="submit" class="btn btn-primary btn-sm" value="Save"></form></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div></div>';

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
