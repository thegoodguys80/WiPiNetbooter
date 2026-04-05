<?php
header("Refresh: 3; url=fwupdate.php");
include_once 'ui_mode.php';

$ip      = $_GET["ip"] ?? '';
$version = $_GET["version"] ?? '';

if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    header("Location: fwupdate.php");
    exit;
}

$fwfile = '';
if ($version === '4.01')      { $fwfile = 'FW_Netdimm_401.bin'; }
elseif ($version === '4.02')  { $fwfile = 'FW_Netdimm_402.bin'; }
elseif ($version === '4.03')  { $fwfile = 'FW_Netdimm_403.bin'; }
else { header("Location: fwupdate.php"); exit; }

$output = shell_exec('sudo python3 /sbin/piforce/webforcefw.py ' . escapeshellarg($fwfile) . ' ' . escapeshellarg($ip));


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Sending Firmware</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('netdimms');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('package') . ' Sending Firmware</h1>';
    echo '<div class="card" style="max-width:560px;">';
    echo '<div class="card-body">';
    echo '<p>Firmware <strong>' . htmlspecialchars($version, ENT_QUOTES, 'UTF-8') . '</strong> sent to <strong>' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '</strong>.</p>';
    echo '<p style="margin-top:8px;">Follow on-screen instructions on the Naomi to complete the upgrade.</p>';
    if ($output) {
        echo '<pre style="margin-top:16px;background:var(--color-surface-hover,#1a1a1a);padding:12px;border-radius:6px;font-size:13px;">' . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    echo '<p style="margin-top:16px;color:var(--color-text-secondary);">Redirecting back…</p>';
    echo '</div></div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
