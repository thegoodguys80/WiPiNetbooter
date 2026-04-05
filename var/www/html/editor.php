<?php
include_once 'ui_mode.php';

$mappingfile = $_GET["mappingfile"] ?? '';
$devicefile  = $_GET["devicefile"] ?? '';
$mode        = $_GET["mode"] ?? '';

// Validate paths — only allow files within known safe directories
$allowed_dirs = ['/etc/openjvs/games/', '/etc/openffb/games/', '/etc/openjvs/devices/'];
$filepath = $mappingfile !== '' ? $mappingfile : $devicefile;

$path_ok = false;
foreach ($allowed_dirs as $dir) {
    if (strpos(realpath($filepath) ?: $filepath, $dir) === 0) {
        $path_ok = true;
        break;
    }
}
if (!$path_ok || !preg_match('/^[a-zA-Z0-9_\-\/\.]+$/', basename($filepath))) {
    header('Location: ' . ($mappingfile !== '' ? ($mode === 'ffb' ? 'ffbmapping.php' : 'mapping.php') : 'devices.php'));
    exit;
}

$title    = $mappingfile !== '' ? 'Mapping File Editor' : 'Device File Editor';
$back_url = $mappingfile !== '' ? ($mode === 'ffb' ? 'ffbmapping.php' : 'mapping.php') : 'devices.php';
$back_lbl = $mappingfile !== '' ? 'Return to Mapping Files' : 'Return to Device Files';

if (isset($_POST['text'])) {
    file_put_contents($filepath, $_POST['text']);
    header('Location: editor.php?' . ($mappingfile !== '' ? 'mappingfile=' . urlencode($mappingfile) . '&mode=' . urlencode($mode) : 'devicefile=' . urlencode($devicefile)));
    exit;
}

$text = file_exists($filepath) ? file_get_contents($filepath) : '';


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '<style>textarea{width:100%;height:28em;font-family:monospace;font-size:13px;background:var(--color-surface-hover,#1a1a1a);color:var(--color-text-primary,#f0f0f0);border:1px solid var(--color-border,#333);border-radius:6px;padding:12px;resize:vertical;}</style>';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>&#9881; ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<p class="page-intro"><code>' . htmlspecialchars(basename($filepath), ENT_QUOTES, 'UTF-8') . '</code></p>';
    echo '<p style="margin-bottom:16px;"><a href="' . htmlspecialchars($back_url, ENT_QUOTES, 'UTF-8') . '" class="btn btn-secondary btn-sm">&#8592; ' . htmlspecialchars($back_lbl, ENT_QUOTES, 'UTF-8') . '</a></p>';
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<form action="" method="post">';
    echo '<textarea name="text">' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</textarea>';
    echo '<div style="margin-top:16px;display:flex;gap:8px;">';
    echo '<input type="submit" class="btn btn-primary" value="Save File">';
    echo '<input type="reset" class="btn btn-secondary" value="Reset">';
    echo '</div></form>';
    echo '</div></div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
