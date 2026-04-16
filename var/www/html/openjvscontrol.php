<?php
include_once 'ui_mode.php';

$mappingfiles = is_dir('/etc/openjvs/games') ? scandir('/etc/openjvs/games') : [];


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - OpenJVS Control</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('gamepad') . ' OpenJVS Control</h1>';
    echo '<p class="page-intro">Run OpenJVS in standalone mode with a selected mapping file.</p>';

    echo '<div class="card" style="max-width:480px;">';
    echo '<div class="card-header"><h2 class="card-title">Launch OpenJVS</h2></div>';
    echo '<div class="card-body">';
    echo '<form method="POST" action="launchopenjvs.php">';
    echo '<label style="display:block;margin-bottom:8px;font-weight:600;">Select Mapping File</label>';
    echo '<select name="mapping" class="form-select" style="margin-bottom:16px;">';
    for ($i = 2; $i < count($mappingfiles); $i++) {
        $name = $mappingfiles[$i];
        echo '<option value="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" class="btn btn-primary" value="Launch OpenJVS">';
    echo '</form>';
    echo '</div></div>';

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
