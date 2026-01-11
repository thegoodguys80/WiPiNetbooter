<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu_include.php';
}

$path = '/boot/roms/';
$files = array_values(array_diff(scandir($path), array('.', '..')));

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Edit Games</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    // Sidebar navigation
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header">';
    echo '<h2>WiPi Netbooter</h2>';
    echo '</div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item active"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav>';
    echo '</div>';
    
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container">';
    echo '<div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()">';
    echo '<span></span><span></span><span></span>';
    echo '</button>';
    
    echo '<h1>✏️ Edit Game List</h1>';
    echo '<p style="margin-bottom: 24px;">Show or hide games from the main game list</p>';
    
    echo '<div class="grid grid-cols-3">';
    
    $f = fopen("csv/romsinfo.csv", "r");
    while (($row = fgetcsv($f)) !== false) {
        if (in_array($row[1], $files)) {
            $toggle = ($row[12] == 'Yes') ? 'No' : 'Yes';
            $isEnabled = ($row[12] == 'Yes');
            
            echo '<div class="card">';
            echo '<div class="card-header">';
            echo '<h3 class="card-title">' . htmlspecialchars($row[4], ENT_QUOTES, 'UTF-8') . '</h3>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<p><strong>Orientation:</strong> ' . htmlspecialchars($row[10], ENT_QUOTES, 'UTF-8') . '</p>';
            echo '<p><strong>Controls:</strong> ' . htmlspecialchars($row[11], ENT_QUOTES, 'UTF-8') . '</p>';
            
            if ($isEnabled) {
                echo '<span class="badge badge-success">✓ Enabled</span>';
            } else {
                echo '<span class="badge badge-secondary">○ Disabled</span>';
            }
            
            echo '<div style="margin-top: 16px;">';
            if ($isEnabled) {
                echo '<a href="updatecsvenable.php?rom=' . urlencode($row[1]) . '&enabled=' . urlencode($toggle) . '" class="btn btn-warning btn-sm">○ Disable</a>';
            } else {
                echo '<a href="updatecsvenable.php?rom=' . urlencode($row[1]) . '&enabled=' . urlencode($toggle) . '" class="btn btn-primary btn-sm">✓ Enable</a>';
            }
            echo '</div>';
            echo '</div></div>';
        }
    }
    fclose($f);
    
    echo '</div>'; // Close grid
    echo '</div></div>'; // Close main-content and container
    
    // Add sidebar toggle script
    echo '<script>';
    echo 'function toggleSidebar() {';
    echo '  const sidebar = document.getElementById("sidebarNav");';
    echo '  const overlay = document.getElementById("sidebarOverlay");';
    echo '  const burger = document.getElementById("burgerBtn");';
    echo '  sidebar.classList.toggle("open");';
    echo '  overlay.classList.toggle("show");';
    echo '  burger.classList.toggle("open");';
    echo '}';
    echo '</script>';
    echo '</body></html>';
} else {
    // Classic UI
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center>';
    echo '<h1><a href="setup.php">Edit Game List</a></h1>';
    echo '<html><body><table class="center" id="options">';
    echo '<tr><th>Game Name</th><th>Orientation</th><th>Controls</th><th>Enabled</th></tr>';
    
    $f = fopen("csv/romsinfo.csv", "r");
    while (($row = fgetcsv($f)) !== false) {
        echo "<tr>";
        foreach ($row as $cell) {
            if (in_array($row[1], $files)) {
                if ($row[12] == 'Yes') {$toggle = 'No';} else {$toggle = 'Yes';}
                echo '<td>'.$row[4].'</td>';
                echo '<td>'.$row[10].'</td>';
                echo '<td>'.$row[11].'</td>';
                echo '<td><a href="updatecsvenable.php?rom='.$row[1].'&enabled='.$toggle.'">'.$row[12].'</a></td>';
                break;
            }
        }
        echo "</tr>";
    }
    fclose($f);
    echo "</table></center></body></html>";
}
?>
