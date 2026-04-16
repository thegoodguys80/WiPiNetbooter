<?php
header("Refresh: 1; url=bluetooth.php?mode=results");
include_once 'ui_mode.php';


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Bluetooth Scan</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('scan') . ' Scanning Bluetooth…</h1>';
    echo '<p class="page-intro">Searching for nearby devices. This may take up to 15 seconds.</p>';
    echo '<div class="card" style="max-width:560px;">';
    echo '<div class="card-body">';
    echo '<pre style="background:var(--color-surface-hover,#1a1a1a);padding:12px;border-radius:6px;font-size:13px;overflow-x:auto;">';
    ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/bluetoothscan.py', 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8') . "\n";
        flush();
    }
    pclose($handle);
    echo '</pre>';
    echo '<p style="margin-top:12px;"><strong>Scan complete.</strong> Redirecting to results…</p>';
    echo '</div></div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
