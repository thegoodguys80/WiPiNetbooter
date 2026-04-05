<?php
include_once 'ui_mode.php';

$filename = $_GET["rom"] ?? '';
$gamename = $_GET["name"] ?? '';
$system   = $_GET["system"] ?? '';
$mapping  = $_GET["mapping"] ?? '';
$ffb      = $_GET["ffb"] ?? '';

function pinger($address) {
    if (!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return false;
    }
    // TCP connect to port 10703.
    // - Success          → NetDIMM is online and ready to receive a game.
    // - ECONNREFUSED(111)→ Device is online but port is closed (game running).
    // - Timeout          → Device is not reachable at all.
    @fsockopen('tcp://' . $address, 10703, $errno, $errstr, 2.0);
    if ($errno === 0 || $errno === 111) {
        return true; // connected or refused — device is present on the network
    }
    return false;
}

$f = fopen("csv/dimms.csv", "r");
fgetcsv($f); // skip header
$onlinedimms = [];
while (($row = fgetcsv($f)) !== false) {
    $dimmname = $row[0];
    $ip       = $row[1];
    $dimmtype = $row[2];

    switch ($dimmtype) {
        case 'Sega Naomi':    $supported = ['Sega Naomi', 'Sammy Atomiswave']; break;
        case 'Sega Naomi2':   $supported = ['Sega Naomi', 'Sega Naomi2', 'Sammy Atomiswave']; break;
        case 'Sega Chihiro':  $supported = ['Sega Chihiro']; break;
        case 'Sega Triforce': $supported = ['Sega Triforce']; break;
        default:              $supported = []; break;
    }

    if (in_array($system, $supported) && pinger($ip)) {
        $onlinedimms[$ip] = $dimmname;
        $onlydimm = $ip;
    }
}
fclose($f);

$count = count($onlinedimms);

$loadpage = (strpos($_SERVER['HTTP_USER_AGENT'] ?? '', 'Chrome') !== false) ? 'load.php' : 'loadnochrome.php';

if ($count === 1) {
    $url = $loadpage . '?rom=' . urlencode($filename) . '&name=' . urlencode($gamename) . '&dimm=' . urlencode($onlydimm) . '&mapping=' . urlencode($mapping) . '&ffb=' . urlencode($ffb);
    header('Location: ' . $url);
    exit;
}

// --- Page output ---
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Launch Game</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';

    echo modern_sliding_sidebar_nav('games');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('rocket') . ' Launch Game</h1>';
    echo '<p class="page-intro">Launching: <strong>' . htmlspecialchars($gamename, ENT_QUOTES, 'UTF-8') . '</strong></p>';

    if ($count === 0) {
        echo '<div class="alert alert-warning" style="max-width:560px;">';
        echo '<strong>No NetDIMMs available</strong> for <em>' . htmlspecialchars($system, ENT_QUOTES, 'UTF-8') . '</em>.<br>';
        echo 'Make sure your NetDIMM is powered on and connected to the network.';
        echo '</div>';
        echo '<a href="gamelist.php" class="btn btn-primary" style="margin-top:16px;">' . arcade_icon('games') . ' Back to Game List</a>';
    } else {
        echo '<div class="card" style="max-width:560px;">';
        echo '<div class="card-header"><h2 class="card-title">' . arcade_icon('netdimms') . ' Select NetDIMM</h2></div>';
        echo '<div class="card-body">';
        echo '<p>Multiple NetDIMMs found for <strong>' . htmlspecialchars($system, ENT_QUOTES, 'UTF-8') . '</strong>. Choose one to launch:</p>';
        echo '<div style="display:flex;flex-direction:column;gap:12px;margin-top:16px;">';
        foreach ($onlinedimms as $ipaddress => $name) {
            $url = htmlspecialchars($loadpage . '?rom=' . urlencode($filename) . '&name=' . urlencode($gamename) . '&dimm=' . urlencode($ipaddress) . '&mapping=' . urlencode($mapping) . '&ffb=' . urlencode($ffb), ENT_QUOTES);
            echo '<a href="' . $url . '" class="btn btn-primary btn-lg">' . arcade_icon('netdimms') . ' ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' <span style="opacity:0.7;font-size:0.85em;">(' . htmlspecialchars($ipaddress, ENT_QUOTES, 'UTF-8') . ')</span></a>';
        }
        echo '</div>';
        echo '</div></div>';
    }

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
