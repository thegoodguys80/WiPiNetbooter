<?php
include_once 'ui_mode.php';

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Controller Reference</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('gamepad') . ' OpenJVS Controller Reference</h1>';
    echo '<p class="page-intro">Mapping keywords and their corresponding controller inputs.</p>';

    echo '<div style="text-align:center;margin-bottom:24px;">';
    echo '<img src="img/openjvs-controller.png" style="max-width:100%;border-radius:8px;">';
    echo '</div>';

    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<table class="table" style="width:100%"><thead><tr><th>Mapping Keyword</th><th>Diagram Label</th><th>Purpose</th><th>Type</th></tr></thead><tbody>';
    $f = fopen("/sbin/piforce/mastermapping.csv", "r");
    while (($line = fgetcsv($f)) !== false) {
        echo '<tr>';
        foreach ($line as $cell) {
            echo '<td>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</td>';
        }
        echo '</tr>';
    }
    fclose($f);
    echo '</tbody></table>';
    echo '</div></div>';

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
