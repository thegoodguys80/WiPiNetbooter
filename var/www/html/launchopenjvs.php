<?php
header("Refresh: 2; url=openjvscontrol.php");
include_once 'ui_mode.php';

$mapping = $_POST['mapping'] ?? '';

// mapping is an openjvs game config filename (e.g. "18-wheeler"), not a device path
if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $mapping)) {
    header("Location: openjvscontrol.php");
    exit;
}

shell_exec('sudo killall -9 openjvs > /dev/null 2>/dev/null &');
shell_exec('sudo openjvs ' . escapeshellarg($mapping) . ' > /dev/null 2>/dev/null &');

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Starting OpenJVS</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<div class="alert alert-info" style="max-width:480px;">';
    echo 'Starting OpenJVS with mapping <strong>' . htmlspecialchars($mapping, ENT_QUOTES, 'UTF-8') . '</strong>… redirecting.';
    echo '</div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
