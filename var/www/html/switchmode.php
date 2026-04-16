<?php
header("Refresh: 1; url=options.php");
include_once 'ui_mode.php';

$mode = $_GET["mode"] ?? '';
if (!preg_match('/^[a-zA-Z0-9_]+$/', $mode)) {
    header("Location: options.php");
    exit;
}

shell_exec('sudo python3 /sbin/piforce/switchmode.py ' . escapeshellarg($mode) . ' > /dev/null 2>/dev/null &');

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Switching Mode</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('options');
    echo '<div class="container p-6">';
    echo '<div class="alert alert-info" style="max-width:480px;">Updating mode to <strong>' . htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') . '</strong>… redirecting.</div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
