<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - ROM Database</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header"><h2>WiPi Netbooter</h2></div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item active"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container"><div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()"><span></span><span></span><span></span></button>';
    echo '<h1>💾 ROM Database</h1>';
    echo '<p style="margin-bottom: 24px;">Raw ROM information from romsinfo.csv</p>';
    
    echo '<div class="card"><div class="card-body">';
    echo '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;">';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<meta name="description" content="Responsive Header Nav">';
    echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    include 'menu.php';
    echo '<section><center><p>';
    echo '<h1><a href="menu.php">ROM Database</a></h1>';
    echo '<table border="1">';
}

if (!file_exists("csv/romsinfo.csv")) {
    if ($ui_mode === 'modern') {
        echo '<div class="card"><div class="card-body">';
        echo '<p style="color: #ff4a4a;">Error: CSV file not found at csv/romsinfo.csv</p>';
        echo '</div></div>';
    } else {
        echo '<p style="color: red;">Error: CSV file not found</p>';
    }
} else {
    $f = fopen("csv/romsinfo.csv", "r");
    $first_row = true;
    while (($line = fgetcsv($f)) !== false) {
    if ($ui_mode === 'modern') {
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
    } else {
        echo '<tr>';
        foreach ($line as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>\n';
    }
    }
    fclose($f);
}

if ($ui_mode === 'modern') {
    echo '</tbody></table></div></div></div>'; // Close tbody, table, overflow div, card-body, card
    echo '</div></div>'; // Close main-content, container
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}</script>';
    echo '</body></html>';
} else {
    echo '</table></p></center></section></body></html>';
}
?>
