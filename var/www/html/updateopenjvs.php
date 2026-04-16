<?php

include_once 'ui_mode.php';

$confirm = $_GET['confirm'] ?? '';

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - OpenJVS Update</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
load_ui_styles();
echo '</head><body class="kiosk-mode">';
echo modern_sliding_sidebar_nav('setup');
echo '<div class="container p-6">';

echo '<h1 class="text-3xl" style="margin-bottom: 24px;">' . arcade_icon('refresh') . ' OpenJVS Updater</h1>';

if ($confirm !== 'yes') {
    echo '<div class="card" style="max-width: 520px;">';
    echo '<div class="card-header"><h2 class="card-title">Update OpenJVS</h2></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">This will update OpenJVS to the latest version from the project repository.</p>';
    echo '<p style="margin-bottom: 24px;"><strong>Are you sure?</strong></p>';
    echo '<div style="display: flex; gap: 12px; flex-wrap: wrap;">';
    echo '<form action="updateopenjvs.php?confirm=yes" method="post"><button type="submit" class="btn btn-primary">' . arcade_icon('lightning') . ' Confirm</button></form>';
    echo '<form action="openjvs.php"><button type="submit" class="btn btn-secondary">Cancel</button></form>';
    echo '</div>';
    echo '</div></div>';
} else {
    echo '<div class="card" style="max-width: 900px;">';
    echo '<div class="card-header"><h2 class="card-title">Update log</h2></div>';
    echo '<div class="card-body">';
    echo '<pre style="white-space: pre-wrap; word-break: break-word; font-size: 13px; line-height: 1.5; max-height: 70vh; overflow: auto; margin: 0; padding: 12px; background: var(--color-surface); border-radius: 8px; border: 1px solid var(--color-border);">';
    ini_set('output_buffering', false);
    $handle = popen('sudo /root/update-openjvs.sh', 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
        flush();
    }
    pclose($handle);
    echo '</pre>';
    echo '<p style="margin-top: 20px;"><a href="openjvs.php" class="btn btn-primary">' . arcade_icon('setup') . ' Return to OpenJVS</a></p>';
    echo '</div></div>';
}

echo '</div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
echo '</body></html>';

?>
