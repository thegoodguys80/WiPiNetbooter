<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu.php';
}

$file = '/boot/config.txt';
$searchfor = 'disable-bt';

$contents = @file_get_contents($file);
$pattern = preg_quote($searchfor, '/');
$pattern = "/^.*$pattern.*\$/m";
if($contents && preg_match_all($pattern, $contents, $matches)){
    $bluetooth = 'disabled';
}
else {
   $bluetooth = 'enabled';
}

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - OpenJVS</title>';
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
    
    echo '<h1>🎮 OpenJVS Configuration</h1>';
    echo '<p style="margin-bottom: 24px;">Configure arcade controller support and device mappings</p>';
    
    echo '<div class="grid grid-cols-2">';
    
    // Device Configuration
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">🎮 Device Setup</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Configure and manage input devices</p>';
    echo '<a href="devicescan.php" class="btn btn-primary btn-block">🔍 Scan and Configure Devices</a>';
    echo '<a href="devices.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">💾 Manage Device Files</a>';
    echo '</div></div>';
    
    // Mapping Configuration
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">🗺️ Game Mappings</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Manage controller-to-game mappings</p>';
    echo '<a href="mapping.php" class="btn btn-primary btn-block">📝 Manage Mapping Files</a>';
    echo '<a href="editmappings.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">✏️ Update Game Mappings</a>';
    echo '</div></div>';
    
    // Bluetooth (conditional)
    if ($bluetooth == 'enabled') {
        echo '<div class="card">';
        echo '<div class="card-header"><h3 class="card-title">🔵 Bluetooth</h3></div>';
        echo '<div class="card-body">';
        echo '<p style="margin-bottom: 16px;">Manage Bluetooth controllers</p>';
        echo '<a href="bluetooth.php?mode=main" class="btn btn-primary btn-block">🔗 Bluetooth Devices</a>';
        echo '</div></div>';
    }
    
    // System Control
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">⚙️ System Control</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">OpenJVS system management</p>';
    echo '<a href="openjvscontrol.php" class="btn btn-primary btn-block">🔧 OpenJVS Control</a>';
    echo '<a href="updateopenjvs.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">🔄 Update OpenJVS</a>';
    echo '<a href="openjvscontroller.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">📚 Controller Reference</a>';
    echo '</div></div>';
    
    echo '</div>';
    
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
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center><p>';
    echo '<h1><a href="setup.php">OpenJVS Configuration Menu</a></h1><br>';
    echo '<div class="box2"><a href="devicescan.php">Scan and Configure Devices</a><br></div><br>';
    echo '<div class="box2"><a href="devices.php">Manage Device Files</a><br></div><br>';
    echo '<div class="box2"><a href="mapping.php">Manage Mapping Files</a><br></div><br>';
    echo '<div class="box2"><a href="editmappings.php">Update Game Mappings</a><br></div><br>';
    
    if ($bluetooth == 'enabled'){
        echo '<div class="box2"><a href="bluetooth.php?mode=main">Bluetooth Devices</a></div><br>';
    }
    
    echo '<div class="box2"><a href="openjvscontrol.php">OpenJVS Control</a><br></div><br>';
    echo '<div class="box2"><a href="updateopenjvs.php">Update OpenJVS</a><br></div><br>';
    echo '<div class="box2"><a href="openjvscontroller.php">Controller Reference</a></div>';
    echo '</p><center></body></html>';
}
?>


