<?php
include_once 'ui_mode.php';
include_once __DIR__ . '/game_lookup.php';

$name_raw = $_GET['name'] ?? '';
$rom_key = $_GET['rom'] ?? '';
if ($rom_key !== '' && !preg_match('/^[a-zA-Z0-9_\-\.\{\}\[\]!]+\.(bin|bin\.gz)$/i', $rom_key)) {
    $rom_key = '';
}

$gload = game_loading_metadata($rom_key, $name_raw);
$display_title = $gload['display_title'] !== '' ? $gload['display_title'] : $name_raw;
if ($display_title === '') {
    $display_title = 'Loading';
}

$loadprogress_qs = 'name=' . urlencode($name_raw);
if ($rom_key !== '') {
    $loadprogress_qs .= '&rom=' . urlencode($rom_key);
}

$handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
$progress = intval(fgets($handle));
pclose($handle);

$complete = ($progress >= 100);
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
    echo '<div class="game-loading" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . (int) $progress . '" aria-busy="' . ($complete ? 'false' : 'true') . '">';
    echo '<div class="game-loading__inner">';
    echo game_loading_art_block_html($gload['art_url'], $display_title);
    echo '<h1 class="game-loading__title">' . htmlspecialchars($display_title, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<div class="load-progress-wrap">';
    echo '<div class="load-progress-bar"><div id="myBar" class="load-progress-fill" style="width:' . $progress . '%">' . $progress . '%</div></div>';
    echo '</div>';
    echo '</div></div></div>';

    if ($complete) {
        echo '<script>setTimeout(function(){window.location="' . $back_url . '";},2000);</script>';
    } else {
        echo '<script>setTimeout(function(){window.location="loadprogress.php?' . $loadprogress_qs . '";},800);</script>';
    }

    echo '</body></html>';
?>
