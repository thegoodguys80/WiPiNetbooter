<?php
include 'ui_mode.php';

// SECURITY: Validate device path input — allow /dev/input/eventN and /dev/ttyUSBN, /dev/ttyACMN
$device_path = $_GET["path"] ?? '';
$valid_path = !empty($device_path) && preg_match('#^/dev/(input/event|ttyUSB|ttyACM)[0-9]+$#', $device_path);
if (!$valid_path) {
        echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Device Configuration</title>';
        echo '<link rel="stylesheet" href="css/modern-theme.css">';
        echo '<link rel="stylesheet" href="css/components.css">';
        echo '<link rel="stylesheet" href="css/arcade-icons.css">';
        echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
        echo '<link rel="stylesheet" href="css/arcade-retro.css">';
        echo '</head><body>';
        echo modern_sliding_sidebar_nav('setup');
        echo '<div class="container p-6">';
        echo '<div class="alert alert-warning"><strong>No device path specified.</strong> Please select a device from the device scan page.</div>';
        echo '<a href="devicescan.php" class="btn btn-primary" style="margin-top:16px;">&#8592; Back to Device Scan</a>';
        echo '</div>';
        echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
        echo '</body></html>';
    exit;
}

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Device Configuration</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container">';
    
    echo '<h1>'.arcade_icon('gamepad').' Device Configuration Wizard</h1>';
    echo '<p style="margin-bottom: 24px;">Configuring device: <code>'.htmlspecialchars($device_path, ENT_QUOTES, 'UTF-8').'</code></p>';
    
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">Controller Mapping</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Press each button on your controller when prompted:</p>';
    echo '<table class="table" style="width: 100%;">';
    echo '<thead><tr><th>Control</th><th>Input</th></tr></thead>';
    echo '<tbody>';

ini_set('output_buffering', false);
$handle = popen('sudo python3 /sbin/piforce/configuration.py ' . escapeshellarg($device_path), 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
}
pclose($handle);

    echo '</tbody></table>';
    echo '</div></div>';
    
    echo '<div style="margin-top: 24px;">';
    echo '<a href="devicescan.php" class="btn btn-primary">← Return to Device Scan</a>';
    echo '<a href="devices.php" class="btn btn-secondary" style="margin-left: 8px;">'.arcade_icon('list').' View Device Files</a>';
    echo '</div>';
    
    echo '</div></div>'; // Close main-content and container
    
    // Add sidebar toggle script
    echo '<script>';
    echo 'function toggleSidebar() {';
    echo '  const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");';
    echo '  if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");';
    echo '}';
    echo '</script>';
    echo '</body></html>';
?>
