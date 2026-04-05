<?php
include_once 'ui_mode.php';

$ip      = $_POST["ip"] ?? '';
$version = $_POST["version"] ?? '';

if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    header("Location: fwupdate.php");
    exit;
}
if (!in_array($version, ['4.01', '4.02', '4.03'])) {
    header("Location: fwupdate.php");
    exit;
}

$ipEsc      = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
$versionEsc = htmlspecialchars($version, ENT_QUOTES, 'UTF-8');
$action     = 'fwupdatesend.php?ip=' . urlencode($ip) . '&version=' . urlencode($version);


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Confirm Firmware Update</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('netdimms');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('package') . ' Confirm Firmware Update</h1>';
    echo '<div class="card" style="max-width:480px;">';
    echo '<div class="card-header"><h2 class="card-title">Are you sure?</h2></div>';
    echo '<div class="card-body">';
    echo '<p>You are about to send firmware <strong>' . $versionEsc . '</strong> to <strong>' . $ipEsc . '</strong>.</p>';
    echo '<div class="alert alert-warning" style="margin-top:16px;"><strong>Do not cut power</strong> during the update or you may break your NetDIMM.</div>';
    echo '</div>';
    echo '<div class="card-footer" style="display:flex;gap:8px;">';
    echo '<form action="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '" method="post"><button type="submit" class="btn btn-primary">' . arcade_icon('lightning') . ' Confirm Update</button></form>';
    echo '<a href="fwupdate.php" class="btn btn-secondary">Cancel</a>';
    echo '</div></div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
