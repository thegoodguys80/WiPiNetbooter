<?php
include 'ui_mode.php';

header("Refresh: 4; url=wifi.php");

// SECURITY: Static command with no user input
$command = 'sudo python3 /sbin/piforce/wifiscan.py &';
shell_exec($command . ' > /dev/null 2>/dev/null &');

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Scanning</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('network');
    echo '<div class="container" style="text-align: center; padding-top: 100px;">';
    echo '<h1>'.arcade_icon('scan').' Scanning WiFi Networks...</h1>';
    echo '<div class="spinner" style="margin: 40px auto; width: 60px; height: 60px; border: 4px solid var(--color-border); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>';
    echo '<p style="color: var(--color-text-secondary);">Please wait while we scan for available networks</p>';
    echo '<style>@keyframes spin { to { transform: rotate(360deg); } }</style>';
    echo '</div></div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
