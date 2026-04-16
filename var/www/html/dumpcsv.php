<?php
include 'ui_mode.php';


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - ROM Database</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container">';
    echo '<h1>'.arcade_icon('romdb').' ROM Database</h1>';
    echo '<p style="margin-bottom: 24px;">Raw ROM information from romsinfo.csv</p>';
    
    echo '<div class="card"><div class="card-body">';
    echo '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;">';


if (!file_exists("csv/romsinfo.csv")) {
    
        echo '<div class="card"><div class="card-body">';
        echo '<p style="color: #ff4a4a;">Error: CSV file not found at csv/romsinfo.csv</p>';
        echo '</div></div>';
    
} else {
    $f = fopen("csv/romsinfo.csv", "r");
    $first_row = true;
    while (($line = fgetcsv($f)) !== false) {
    
        if ($first_row) {
            echo '<thead><tr style="background: #2a2a2a;">';
            foreach ($line as $cell) {
                echo '<th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">' . htmlspecialchars($cell) . '</th>';
            }
            echo '</tr></thead><tbody>';
            $first_row = false;
        } else {
            echo '<tr style="border-bottom: 1px solid #333;">';
            foreach ($line as $cell) {
                echo '<td style="padding: 12px;">' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }
    
    }
    fclose($f);
}


    echo '</tbody></table></div></div></div>'; // Close tbody, table, overflow div, card-body, card
    echo '</div></div>'; // Close main-content, container
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
