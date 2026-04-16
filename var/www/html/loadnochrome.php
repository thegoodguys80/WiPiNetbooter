<?php
set_time_limit(0);
include_once 'ui_mode.php';

$relaymode = trim(file_get_contents('/sbin/piforce/relaymode.txt'));
$zeromode  = trim(file_get_contents('/sbin/piforce/zeromode.txt'));
$openmode  = trim(file_get_contents('/sbin/piforce/openmode.txt'));
$ffbmode   = trim(file_get_contents('/sbin/piforce/ffbmode.txt'));

$rom     = basename($_GET["rom"] ?? '');
$name    = $_GET["name"] ?? '';
$dimm    = $_GET["dimm"] ?? '';
$mapping = $_GET["mapping"] ?? '';
$ffb     = $_GET["ffb"] ?? '';

// Validate ROM filename
if (!preg_match('/^[a-zA-Z0-9_\-\.\{\}\[\]!]+$/', $rom)) {
    header("Location: gamelist.php");
    exit;
}

// Validate DIMM IP
if (!filter_var($dimm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    header("Location: gamelist.php");
    exit;
}

// Validate mapping/ffb config names
if ($mapping !== '' && !preg_match('/^[a-zA-Z0-9_\-]+$/', $mapping)) { $mapping = ''; }
if ($ffb !== '' && !preg_match('/^[a-zA-Z0-9_\-]+$/', $ffb))         { $ffb = ''; }

include_once __DIR__ . '/game_lookup.php';
$gload = game_loading_metadata($rom, $name);
$display_title = $gload['display_title'] !== '' ? $gload['display_title'] : $name;

$back_url = 'gamelist.php';


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Loading</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '<link rel="stylesheet" href="css/game-loading.css">';
    echo '</head><body class="game-loading-page game-loading-page--minimal">';
    echo '<div class="container p-6">';
    echo '<div class="game-loading" role="progressbar" aria-busy="true">';
    echo '<div class="game-loading__inner">';
    echo game_loading_art_block_html($gload['art_url'], $display_title);
    echo '<h1 class="game-loading__title">' . htmlspecialchars($display_title, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<div class="load-progress-wrap">';
    echo '<div class="load-progress-bar"><div id="myBar" class="load-progress-fill">…</div></div>';
    echo '</div>';
    echo '</div></div></div>';
    echo '<script>setTimeout(function(){window.location="loadprogress.php?name=' . urlencode($name) . '&rom=' . urlencode($rom) . '";},1);</script>';
    echo '</body></html>';


// Launch webforce in background
$command = 'sudo python3 /sbin/piforce/webforce.py ' .
    escapeshellarg($rom) . ' ' .
    escapeshellarg($dimm) . ' ' .
    escapeshellarg($relaymode) . ' ' .
    escapeshellarg($zeromode) . ' ' .
    escapeshellarg($mapping) . ' ' .
    escapeshellarg($ffb);
shell_exec($command . ' > /dev/null 2>/dev/null &');
?>
