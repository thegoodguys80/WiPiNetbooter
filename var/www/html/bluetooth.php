<?php
include_once 'ui_mode.php';

$mode = $_GET['mode'] ?? 'main';

$connected = shell_exec('sudo bluetoothctl devices') ?? '';
$btarray   = array_filter(explode('Device ', $connected));

$msg = '';
$msgType = 'info';

if ($mode === 'results' && isset($_POST["pair"])) {
    $mac = $_POST["mac"] ?? '';
    if (!preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac)) {
        $msg = 'Invalid MAC address format.';
        $msgType = 'warning';
    } else {
        ob_start();
        $handle = popen('sudo python3 /sbin/piforce/bluetoothpair.py add ' . escapeshellarg($mac), 'r');
        $i = 0;
        while (!feof($handle) && $i <= 10) { $i++; ob_end_clean(); ob_start(); fgets($handle, 2000); }
        pclose($handle);
        ob_end_clean();
        $msg = 'Pairing attempt complete for <strong>' . htmlspecialchars($mac, ENT_QUOTES, 'UTF-8') . '</strong>.';
        $msgType = 'success';
    }
}

if ($mode === 'results' && isset($_POST["remove"])) {
    $mac = $_POST["mac"] ?? '';
    if (!preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac)) {
        $msg = 'Invalid MAC address format.';
        $msgType = 'warning';
    } else {
        $handle = popen('sudo python3 /sbin/piforce/bluetoothpair.py remove ' . escapeshellarg($mac), 'r');
        while (!feof($handle)) { fgets($handle, 2000); }
        pclose($handle);
        $msg = 'Device <strong>' . htmlspecialchars($mac, ENT_QUOTES, 'UTF-8') . '</strong> removed.';
        $msgType = 'success';
    }
}


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Bluetooth</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>&#9654; Bluetooth Devices</h1>';

    if ($mode === 'main') {
        echo '<p class="page-intro">Pair Bluetooth controllers for use with OpenJVS.</p>';
        echo '<div class="card" style="max-width:560px;margin-bottom:24px;">';
        echo '<div class="card-header"><h2 class="card-title">Instructions</h2></div>';
        echo '<div class="card-body">';
        echo '<p>Put your device in discovery mode, then start a scan. The Pi will search for 15 seconds.</p>';
        echo '</div><div class="card-footer">';
        echo '<a href="bluetoothscan.php" class="btn btn-primary">' . arcade_icon('scan') . ' Start Scan</a>';
        echo '</div></div>';

        echo '<div class="card" style="max-width:560px;">';
        echo '<div class="card-header"><h2 class="card-title">Known Devices</h2></div>';
        echo '<div class="card-body">';
        if ($connected) {
            echo '<ul style="list-style:none;padding:0;margin:0;">';
            foreach ($btarray as $device) {
                if ($device !== '') {
                    echo '<li style="padding:8px 0;border-bottom:1px solid var(--color-border,#333);">' . htmlspecialchars(trim(substr($device, 17)), ENT_QUOTES, 'UTF-8') . '</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p class="text-secondary">No devices detected.</p>';
        }
        echo '</div></div>';

    } else { // results mode
        if ($msg) {
            echo '<div class="alert alert-' . $msgType . '" style="max-width:560px;margin-bottom:16px;">' . $msg . '</div>';
        }

        echo '<div class="card" style="max-width:560px;">';
        echo '<div class="card-header"><h2 class="card-title">Pair or Remove Device</h2></div>';
        echo '<div class="card-body">';
        echo '<p style="margin-bottom:16px;">Detected devices:</p>';
        if ($connected) {
            echo '<ul style="list-style:none;padding:0;margin:0 0 16px 0;">';
            foreach ($btarray as $device) {
                if ($device !== '') {
                    echo '<li style="padding:6px 0;">' . htmlspecialchars(trim(substr($device, 17)), ENT_QUOTES, 'UTF-8') . '</li>';
                }
            }
            echo '</ul>';
            echo '<form action="bluetooth.php?mode=results" method="post">';
            echo '<select name="mac" class="form-select" style="margin-bottom:16px;">';
            foreach ($btarray as $value) {
                if ($value !== '') {
                    $mac  = substr($value, 0, 17);
                    $name = trim(substr($value, 17));
                    echo '<option value="' . htmlspecialchars($mac, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</option>';
                }
            }
            echo '</select><br>';
            echo '<div style="display:flex;gap:8px;flex-wrap:wrap;">';
            echo '<input type="submit" class="btn btn-primary" name="pair" value="Pair Device">';
            echo '<input type="submit" class="btn btn-warning" name="remove" value="Remove Device">';
            echo '<a href="bluetoothscan.php" class="btn btn-secondary">Rescan</a>';
            echo '</div></form>';
        } else {
            echo '<p class="text-secondary">No devices found. <a href="bluetoothscan.php">Run a scan</a>.</p>';
        }
        echo '</div></div>';
    }

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';


?>
