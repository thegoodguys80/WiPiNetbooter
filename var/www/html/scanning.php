<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

header("Refresh: 4; url=wifi.php");

if ($ui_mode !== 'modern') {
    include 'menu.php';
}

// SECURITY: Static command with no user input
$command = 'sudo python /sbin/piforce/wifiscan.py &';
shell_exec($command . ' > /dev/null 2>/dev/null &');

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Scanning</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    echo '<div class="container">';
    echo '<div class="main-content" style="text-align: center; padding-top: 100px;">';
    echo '<h1>📡 Scanning WiFi Networks...</h1>';
    echo '<div class="spinner" style="margin: 40px auto; width: 60px; height: 60px; border: 4px solid var(--color-border); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>';
    echo '<p style="color: var(--color-text-secondary);">Please wait while we scan for available networks</p>';
    echo '<style>@keyframes spin { to { transform: rotate(360deg); } }</style>';
    echo '</div></div>';
    echo '</body></html>';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center>';
    echo '<h1>Scanning WiFi ...</h1>';
    echo '</center></section></body></html>';
}
?>
