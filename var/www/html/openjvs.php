<?php
include 'ui_mode.php';

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

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - OpenJVS</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container">';
    
    echo '<h1>'.arcade_icon('gamepad').' OpenJVS Configuration</h1>';
    echo '<p style="margin-bottom: 24px;">Configure arcade controller support and device mappings</p>';
    
    echo '<div class="grid grid-cols-2">';
    
    // Device Configuration
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('gamepad').' Device Setup</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Configure and manage input devices</p>';
    echo '<a href="devicescan.php" class="btn btn-primary btn-block">'.arcade_icon('scan').' Scan and Configure Devices</a>';
    echo '<a href="devices.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">'.arcade_icon('list').' Manage Device Files</a>';
    echo '</div></div>';
    
    // Mapping Configuration
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('list').' Game Mappings</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Manage controller-to-game mappings</p>';
    echo '<a href="mapping.php" class="btn btn-primary btn-block">'.arcade_icon('edit').' Manage Mapping Files</a>';
    echo '<a href="editmappings.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">'.arcade_icon('edit').' Update Game Mappings</a>';
    echo '</div></div>';
    
    // Bluetooth (conditional)
    if ($bluetooth == 'enabled') {
        echo '<div class="card">';
        echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('bluetooth').' Bluetooth</h3></div>';
        echo '<div class="card-body">';
        echo '<p style="margin-bottom: 16px;">Manage Bluetooth controllers</p>';
        echo '<a href="bluetooth.php?mode=main" class="btn btn-primary btn-block">'.arcade_icon('bluetooth').' Bluetooth Devices</a>';
        echo '</div></div>';
    }
    
    // System Control
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('setup').' System Control</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">OpenJVS system management</p>';
    echo '<a href="openjvscontrol.php" class="btn btn-primary btn-block">'.arcade_icon('tools').' OpenJVS Control</a>';
    echo '<a href="updateopenjvs.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">'.arcade_icon('refresh').' Update OpenJVS</a>';
    echo '<a href="openjvscontroller.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">'.arcade_icon('help').' Controller Reference</a>';
    echo '</div></div>';
    
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


