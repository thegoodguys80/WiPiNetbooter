<?php

include_once 'ui_mode.php';

$path = $_GET['path'] ?? '';
if ($path === '') {
    header('Location: audithome.php');
    exit;
}

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - ROM Audit Scan</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
load_ui_styles();
echo '</head><body class="kiosk-mode">';
echo modern_sliding_sidebar_nav('setup');
echo '<div class="container p-6">';

echo '<h1 class="text-3xl" style="margin-bottom: 8px;">' . arcade_icon('scan') . ' ROM Audit Scan</h1>';
echo '<p class="page-intro" style="margin-bottom: 24px;">Scanning: <code>' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '</code></p>';

echo '<div class="card" style="max-width: 960px;">';
echo '<div class="card-body">';
echo '<pre style="white-space: pre-wrap; word-break: break-word; font-size: 13px; line-height: 1.5; max-height: 65vh; overflow: auto; margin: 0; padding: 12px; background: var(--color-surface); border-radius: 8px; border: 1px solid var(--color-border);">';
ini_set('output_buffering', false);
$handle = popen('sudo python3 /sbin/piforce/auditnames.py ' . escapeshellarg($path), 'r');
while (!feof($handle)) {
    $buffer = fgets($handle);
    echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
    flush();
}
pclose($handle);
echo '</pre>';
echo '<p style="margin-top: 20px;"><a href="auditresults.php" class="btn btn-primary">' . arcade_icon('dashboard') . ' Go to detailed results</a>';
echo ' <a href="audithome.php" class="btn btn-secondary">Back</a></p>';
echo '</div></div>';

echo '</div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
echo '</body></html>';

?>
