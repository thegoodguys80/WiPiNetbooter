<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu.php';
}

// SECURITY: Validate device path input
$device_path = $_GET["path"] ?? '';
if (empty($device_path) || !preg_match('#^/dev/input/event[0-9]+$#', $device_path)) {
    die('Error: Invalid device path');
}

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Device Configuration</title>';
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
    
    echo '<h1>🎮 Device Configuration Wizard</h1>';
    echo '<p style="margin-bottom: 24px;">Configuring device: <code>'.htmlspecialchars($device_path, ENT_QUOTES, 'UTF-8').'</code></p>';
    
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">Controller Mapping</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Press each button on your controller when prompted:</p>';
    echo '<table class="table" style="width: 100%;">';
    echo '<thead><tr><th>Control</th><th>Input</th></tr></thead>';
    echo '<tbody>';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center><p>';
    echo '<table class="center" id="options">';
    echo '<tr><th>Control</th><th>Input</th></tr>';
}

ini_set('output_buffering', false);
$handle = popen('sudo python3 /sbin/piforce/configuration.py ' . escapeshellarg($device_path), 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
}
pclose($handle);

if ($ui_mode === 'modern') {
    echo '</tbody></table>';
    echo '</div></div>';
    
    echo '<div style="margin-top: 24px;">';
    echo '<a href="devicescan.php" class="btn btn-primary">← Return to Device Scan</a>';
    echo '<a href="devices.php" class="btn btn-secondary" style="margin-left: 8px;">💾 View Device Files</a>';
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
    echo '</table>';
    echo '<br><a href="devicescan.php">Return to Device Scan</a>';
}
?>
