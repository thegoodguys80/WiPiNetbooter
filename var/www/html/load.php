<?php
set_time_limit(0);
include_once 'ui_mode.php';

function gzfilesize($filename) {
    $gzfs = false;
    if (($zp = fopen($filename, 'r')) !== false) {
        if (@fread($zp, 2) == "\x1F\x8B") {
            fseek($zp, -4, SEEK_END);
            if (strlen($datum = @fread($zp, 4)) == 4)
                extract(unpack('Vgzfs', $datum));
        } else {
            $gzfs = filesize($filename);
        }
        fclose($zp);
    }
    return $gzfs;
}

$relaymode = trim(file_get_contents('/sbin/piforce/relaymode.txt'));
$zeromode  = trim(file_get_contents('/sbin/piforce/zeromode.txt'));
$openmode  = trim(file_get_contents('/sbin/piforce/openmode.txt'));
$ffbmode   = trim(file_get_contents('/sbin/piforce/ffbmode.txt'));

$rom     = $_GET["rom"] ?? '';
$name    = $_GET["name"] ?? '';
$dimm    = $_GET["dimm"] ?? '';
$mapping = $_GET["mapping"] ?? '';
$ffb     = $_GET["ffb"] ?? '';

// Validate ROM filename
if (!preg_match('/^[a-zA-Z0-9_\-\.\{\}\[\]!]+\.(bin|bin\.gz)$/i', $rom)) {
    die('Error: Invalid ROM filename format');
}

// Validate ROM exists
$rompath = '/boot/roms/' . basename($rom);
if (!file_exists($rompath)) {
    die('Error: ROM file not found');
}

// Validate IP address
if (!filter_var($dimm, FILTER_VALIDATE_IP)) {
    die('Error: Invalid IP address format');
}

// Validate mapping/ffb — these are OpenJVS/OpenFFB config names (e.g. "18-wheeler", "generic-driving")
// or empty — NOT device paths
if (!empty($mapping) && !preg_match('/^[a-zA-Z0-9_\-]+$/', $mapping)) {
    die('Error: Invalid mapping name');
}
if (!empty($ffb) && !preg_match('/^[a-zA-Z0-9_\-]+$/', $ffb)) {
    die('Error: Invalid FFB name');
}

// Validate relay/zero modes
if (!in_array($relaymode, ['relayon', 'relayoff'])) { $relaymode = 'relayoff'; }
if (!in_array($zeromode,  ['hackon', 'hackoff']))   { $zeromode  = 'hackoff'; }

include_once __DIR__ . '/game_lookup.php';
$gload = game_loading_metadata($rom, $name);
$display_title = $gload['display_title'] !== '' ? $gload['display_title'] : $name;

$filesize = gzfilesize($rompath);

/** Inline script: progress bar width only */
function load_screen_progress_js($progress) {
    $p = (int) $progress;
    return '<script>(function(p){var e=document.getElementById("myBar");if(e){e.style.width=p+"%";e.textContent=p+"%";}})(' . $p . ');</script>';
}

// Page header

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Loading</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '<link rel="stylesheet" href="css/game-loading.css">';
    echo '</head><body class="game-loading-page game-loading-page--minimal">';
    echo '<div class="container p-6">';
    echo '<div class="game-loading" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-busy="true">';
    echo '<div class="game-loading__inner">';
    echo game_loading_art_block_html($gload['art_url'], $display_title);
    echo '<h1 class="game-loading__title">' . htmlspecialchars($display_title, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<div class="load-progress-wrap">';
    echo '<div class="load-progress-bar"><div id="myBar" class="load-progress-fill">0%</div></div>';
    echo '</div>';
    echo '</div></div>';


// Reset progress file before launch to avoid stale COMPLETE value
file_put_contents('/var/log/progress.txt', "0\n");

// Launch webforce in background
$command = 'sudo python3 /sbin/piforce/webforce.py ' .
    escapeshellarg($rom) . ' ' .
    escapeshellarg($dimm) . ' ' .
    escapeshellarg($relaymode) . ' ' .
    escapeshellarg($zeromode) . ' ' .
    escapeshellarg($mapping) . ' ' .
    escapeshellarg($ffb) .
    ' > /dev/null 2>/dev/null &';
shell_exec($command);

// Poll progress — timeout after 5 minutes
ini_set('output_buffering', false);
$last = 0;
$progress = 0;
$timeout = 300; // seconds
$elapsed = 0;
while ($progress < 100 && $elapsed < $timeout) {
    $handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
    $raw = trim(fgets($handle));
    pclose($handle);
    $progress = ($raw === 'COMPLETE') ? 100 : intval($raw);
    if ($progress > $last) {
        
            echo load_screen_progress_js($progress);
        
        ob_flush();
        flush();
        $last = $progress;
    }
    sleep(1);
    $elapsed++;
}

if ($elapsed >= $timeout) {
    
        echo '<script>(function(){var e=document.getElementById("myBar");if(e){e.style.width="0%";e.textContent="—";}var g=document.querySelector(".game-loading");if(g){g.setAttribute("aria-busy","false");}})();</script>';
    
    ob_flush(); flush();
    exit;
}


    echo load_screen_progress_js(100);
    echo '<script>var g=document.querySelector(".game-loading");if(g){g.setAttribute("aria-busy","false");}</script>';


$back_url = 'gamelist.php';

    echo '</div>'; // container


echo '<script>setTimeout(function(){window.location="' . $back_url . '";},2000);</script>';
echo '</body></html>';
?>
