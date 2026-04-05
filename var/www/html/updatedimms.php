<?php
header('Refresh: 1; url=dimms.php');
include_once 'ui_mode.php';

$filename = 'csv/dimms.csv';
$linenum = intval($_GET['linenum'] ?? 0);
$action = $_GET['action'] ?? '';

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - NetDIMM</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
load_ui_styles();
echo '</head><body class="kiosk-mode">';
echo modern_sliding_sidebar_nav('netdimms');
echo '<div class="container p-6">';

if ($action === 'delete') {
    $name = htmlspecialchars($_GET['name'] ?? '', ENT_QUOTES, 'UTF-8');
    echo '<h1 class="text-2xl" style="margin-bottom: 16px;">' . arcade_icon('netdimms') . ' Deleting NetDIMM</h1>';
    echo '<p class="text-lg" style="margin-bottom: 16px;">Removing <strong>' . $name . '</strong>…</p>';
    DelLine($filename, $linenum);
}

if ($action === 'update') {
    $name = htmlspecialchars($_GET['name'] ?? '', ENT_QUOTES, 'UTF-8');
    echo '<h1 class="text-2xl" style="margin-bottom: 16px;">' . arcade_icon('netdimms') . ' Updating NetDIMM</h1>';
    echo '<p class="text-lg" style="margin-bottom: 16px;">Saving <strong>' . $name . '</strong>…</p>';
    $n = $_GET['name'] ?? '';
    $ip = $_GET['ip'] ?? '';
    $type = $_GET['type'] ?? '';
    $update = $n . ',' . $ip . ',' . $type . "\n";
    UpdateLine($filename, $linenum, $update);
}

echo '</div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
echo '</body></html>';

function DelLine($filename, $linenum)
{
    $arr = file($filename);
    unset($arr[(string) $linenum]);

    if (!$fp = fopen($filename, 'w+')) {
        echo '<div class="alert alert-error">Cannot open file.</div>';
        return;
    }

    foreach ($arr as $line) {
        fwrite($fp, $line);
    }
    fclose($fp);

    echo '<div class="alert alert-success" style="max-width: 480px;">Entry was deleted successfully. Redirecting…</div>';
}

function UpdateLine($filename, $linenum, $update)
{
    $arr = file($filename);
    $arr[(string) $linenum] = $update;

    if (!$fp = fopen($filename, 'w+')) {
        echo '<div class="alert alert-error">Cannot open file.</div>';
        return;
    }

    foreach ($arr as $line) {
        fwrite($fp, $line);
    }
    fclose($fp);

    echo '<div class="alert alert-success" style="max-width: 480px;">Entry was updated successfully. Redirecting…</div>';
}

?>
