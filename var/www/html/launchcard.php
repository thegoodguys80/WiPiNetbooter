<?php
header("Refresh: 2; url=cardemulator.php?mode=main");
include_once 'ui_mode.php';

$card       = $_GET["card"] ?? '';
$mode       = $_GET["mode"] ?? '';
$launchmode = $_GET["launchmode"] ?? '';

if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $card)) { die('Error: Invalid card filename'); }
$allowed_modes = ['idas', 'id2', 'id3', 'fzero', 'mkgp', 'wmmt'];
if (!in_array($mode, $allowed_modes)) { die('Error: Invalid card mode'); }
if (!in_array($launchmode, ['manual', 'auto'])) { die('Error: Invalid launch mode'); }

$emumode    = in_array($mode, ['idas', 'id2', 'id3']) ? 'id' : $mode;
$devices    = glob('/dev/ttyUSB*') ?: [];
$dropfolder = '/var/log/activecard';
$isdirempty = is_dir($dropfolder) ? !(new \FilesystemIterator($dropfolder))->valid() : true;
$emuport    = '';
$statusMsg  = '';
$statusType = 'info';

if (@readlink("/dev/COM1")) {
    $compath = '/dev/' . readlink("/dev/COM1");
    foreach ($devices as $device) {
        if ($device !== $compath) { $emuport = $device; }
    }
} else {
    $emuport = '/dev/ttyUSB0';
}

if ($launchmode === 'manual') {
    if (empty($devices) || $emuport === '') {
        $statusMsg  = 'No serial adaptor detected. Please check connections.';
        $statusType = 'warning';
    } else {
        if (!preg_match('#^/dev/ttyUSB[0-9]+$#', $emuport)) { die('Error: Invalid serial device path'); }
        $cardlog_path = '/boot/config/cards/' . $mode . '/' . $card;
        shell_exec('sudo python3 /sbin/piforce/card_emulator/cardlog.py ' . escapeshellarg($cardlog_path) . ' > /dev/null 2>/dev/null &');
        if ($emumode === 'id') {
            $cmd = 'sudo python3 /sbin/piforce/card_emulator/' . $emumode . 'cardemu.py -cp ' . escapeshellarg($emuport) . ' -f ' . escapeshellarg('/boot/config/cards/' . $mode . '/' . $card) . ' -m ' . escapeshellarg($mode);
        } else {
            $cmd = 'sudo python3 /sbin/piforce/card_emulator/' . $emumode . 'cardemu.py -cp ' . escapeshellarg($emuport) . ' -f ' . escapeshellarg('/boot/config/cards/' . $mode . '/' . $card);
        }
        shell_exec($cmd . ' > /dev/null 2>/dev/null &');
        $statusMsg  = 'Card emulator starting on ' . htmlspecialchars($emuport, ENT_QUOTES, 'UTF-8') . ' with card <strong>' . htmlspecialchars($card, ENT_QUOTES, 'UTF-8') . '</strong>.';
        $statusType = 'success';
    }
}

if ($launchmode === 'auto') {
    exec("ps -ax | grep -i cardemu | grep -v grep", $pids);
    if (empty($pids)) {
        $statusMsg  = 'Card emulator is not running. Card cannot be inserted yet!';
        $statusType = 'warning';
    } elseif ($isdirempty) {
        $source = '/boot/config/cards/' . $mode . '/' . $card;
        if (!file_exists($source)) { die('Error: Card file not found'); }
        shell_exec('sudo cp ' . escapeshellarg($source) . ' ' . escapeshellarg($dropfolder . '/' . $card) . ' > /dev/null 2>/dev/null &');
        file_put_contents('/sbin/piforce/nfcwriteback.txt', 'no');
        $statusMsg  = 'Inserting card <strong>' . htmlspecialchars($card, ENT_QUOTES, 'UTF-8') . '</strong>…';
        $statusType = 'success';
    } else {
        $statusMsg  = 'Existing card detected. Card cannot be inserted yet!';
        $statusType = 'warning';
    }
}


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Launch Card</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('card') . ' Launching Card</h1>';
    if ($statusMsg) {
        echo '<div class="alert alert-' . $statusType . '" style="max-width:560px;">' . $statusMsg . '</div>';
    }
    echo '<p style="margin-top:16px;color:var(--color-text-secondary);">Redirecting back to card emulator…</p>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
