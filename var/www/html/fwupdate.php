<?php
include_once 'ui_mode.php';

function pinger($address) {
    exec('fping -c1 -t500 ' . escapeshellarg($address), $output, $status);
    return $status === 0;
}

$f = fopen("csv/dimms.csv", "r");
fgetcsv($f); // skip header
$row = fgetcsv($f);
$empty = ($row === false || $row[1] == null);
$dimms = [];
if (!$empty) {
    rewind($f);
    fgetcsv($f);
    while (($row = fgetcsv($f)) !== false) {
        $dimms[] = $row;
    }
}
fclose($f);


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Firmware Update</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('netdimms');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('package') . ' NetDIMM Firmware Update</h1>';
    echo '<p class="page-intro">Update the firmware on your Type 1 NetDIMM.</p>';

    echo '<div class="card" style="max-width:640px;margin-bottom:24px;">';
    echo '<div class="card-header"><h2 class="card-title">Firmware Versions</h2></div>';
    echo '<div class="card-body">';
    echo '<div class="alert alert-warning"><strong>IMPORTANT:</strong> If running v3.03 or v3.17, you must update to v4.01 first before v4.02. Do not cut power during the update.</div>';
    echo '<ul style="margin-top:12px;">';
    echo '<li><strong>4.01</strong> — Sega CF card adapter support</li>';
    echo '<li><strong>4.02</strong> — Third party CF card support</li>';
    echo '<li><strong>4.03</strong> — CD-R media support</li>';
    echo '</ul>';
    echo '</div></div>';

    if ($empty) {
        echo '<div class="alert alert-warning" style="max-width:640px;">No NetDIMMs configured. <a href="dimms.php">Manage NetDIMMs</a></div>';
    } else {
        foreach ($dimms as $row) {
            $online = pinger($row[1]);
            echo '<div class="card" style="max-width:480px;margin-bottom:16px;">';
            echo '<div class="card-header"><h3 class="card-title">' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '</h3></div>';
            echo '<div class="card-body">';
            echo '<p><strong>IP:</strong> ';
            if ($online) {
                echo '<span class="badge badge-success">' . htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8') . ' ONLINE</span>';
            } else {
                echo '<span class="badge badge-warning">' . htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8') . ' OFFLINE</span>';
            }
            echo '</p>';
            echo '<p style="margin-top:8px;"><strong>Type:</strong> ' . htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8') . '</p>';
            if ($online) {
                echo '<form action="fwupdateconfirm.php" method="post" style="margin-top:16px;">';
                echo '<input type="hidden" name="ip" value="' . htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8') . '">';
                echo '<label style="font-weight:600;">Firmware Version</label>';
                echo '<select name="version" class="form-select" style="margin:8px 0 16px;">';
                echo '<option value="4.01">4.01</option><option value="4.02">4.02</option><option value="4.03">4.03</option>';
                echo '</select><br>';
                echo '<input type="submit" class="btn btn-primary" value="Update Firmware">';
                echo '</form>';
            } else {
                echo '<div style="margin-top:12px;"><a href="fwupdate.php" class="btn btn-secondary">Retry</a></div>';
            }
            echo '</div></div>';
        }
    }

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
