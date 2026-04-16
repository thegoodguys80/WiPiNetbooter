<?php
mb_internal_encoding('UTF-8');
include_once 'ui_mode.php';

$mode = $_GET['mode'] ?? 'main';
$command = $_GET['command'] ?? '';

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Card Actions</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
load_ui_styles();
echo '</head><body class="kiosk-mode">';
echo modern_sliding_sidebar_nav('setup');
echo '<div class="container p-6">';

if ($mode === 'main') {
    echo '<h1 class="text-3xl" style="margin-bottom: 8px;">' . arcade_icon('cards') . ' <a href="setup.php" style="text-decoration: none; color: inherit;">Card Management</a></h1>';
} else {
    echo '<h1 class="text-3xl" style="margin-bottom: 8px;">' . arcade_icon('cards') . ' <a href="cardmanagement.php?mode=main" style="text-decoration: none; color: inherit;">Card Management</a></h1>';
}

$emumode = file_get_contents('/sbin/piforce/emumode.txt');
$nfcmode = file_get_contents('/sbin/piforce/nfcmode.txt');

echo '<p style="color: var(--color-text-secondary); margin-bottom: 24px;">Mode: <code>' . htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') . '</code> · Emu: <span class="badge badge-primary">' . htmlspecialchars(trim($emumode), ENT_QUOTES, 'UTF-8') . '</span></p>';

if ($command === 'nfcwipe') {
    $copyfile = $_GET['filetocopy'] ?? '';
    $path_parts = pathinfo($copyfile);
    $phpfile = '/var/www/html/cards/' . $mode . '/' . $path_parts['filename'] . '.printdata.php';

    echo '<div class="card" style="max-width: 720px;">';
    echo '<div class="card-header"><h2 class="card-title">Wiping card data</h2></div>';
    echo '<div class="card-body">';
    echo '<pre style="white-space: pre-wrap; word-break: break-word; font-size: 13px; line-height: 1.5; max-height: 50vh; overflow: auto; margin: 0 0 16px 0; padding: 12px; background: var(--color-surface); border-radius: 8px; border: 1px solid var(--color-border);">';
    ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/card_emulator/nfcwipe.py', 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
        flush();
    }
    pclose($handle);
    echo '</pre>';
    echo '<a href="cardmanagement.php?mode=' . htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') . '" class="btn btn-primary">' . arcade_icon('cards') . ' Return to Card Management</a>';
    echo '</div></div>';
}

if ($command === 'nfcwrite') {
    $copyfile = $_GET['filetocopy'] ?? '';
    $path_parts = pathinfo($copyfile);
    $phpfile = '/var/www/html/cards/' . $mode . '/' . $path_parts['filename'] . '.printdata.php';

    echo '<div class="card" style="max-width: 720px;">';
    echo '<div class="card-header"><h2 class="card-title">NFC write</h2></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 12px;"><strong>Wiping existing data…</strong></p>';
    echo '<pre style="white-space: pre-wrap; word-break: break-word; font-size: 13px; line-height: 1.5; max-height: 35vh; overflow: auto; margin: 0 0 16px 0; padding: 12px; background: var(--color-surface); border-radius: 8px; border: 1px solid var(--color-border);">';
    ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/card_emulator/nfcwipe.py', 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
        flush();
    }
    pclose($handle);
    echo '</pre>';

    echo '<p style="margin-bottom: 12px;"><strong>Writing new card data…</strong></p>';
    echo '<pre style="white-space: pre-wrap; word-break: break-word; font-size: 13px; line-height: 1.5; max-height: 35vh; overflow: auto; margin: 0 0 16px 0; padding: 12px; background: var(--color-surface); border-radius: 8px; border: 1px solid var(--color-border);">';
    $handle = popen('sudo python3 /sbin/piforce/card_emulator/nfcwrite.py ' . escapeshellarg($copyfile) . ' ' . escapeshellarg($phpfile), 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
        flush();
    }
    pclose($handle);
    echo '</pre>';
    echo '<a href="cardmanagement.php?mode=' . htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') . '" class="btn btn-primary">' . arcade_icon('cards') . ' Return to Card Management</a>';
    echo '</div></div>';
}

if ($command === 'nfc_check') {
    echo '<div class="card" style="max-width: 720px;">';
    echo '<div class="card-header"><h2 class="card-title">NFC check</h2></div>';
    echo '<div class="card-body">';
    echo '<pre style="white-space: pre-wrap; word-break: break-word; font-size: 13px; line-height: 1.5; max-height: 40vh; overflow: auto; margin: 0 0 16px 0; padding: 12px; background: var(--color-surface); border-radius: 8px; border: 1px solid var(--color-border);">';
    ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/card_emulator/nfccheck.py', 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
        flush();
    }
    pclose($handle);
    echo '</pre>';

    $nfc_check = file_get_contents('/var/log/cardcheck/NFC_Check');
    if (trim($nfc_check) === 'none') {
        echo '<div class="alert alert-warning">No valid save data found.</div>';
    } else {
        echo '<p style="margin-bottom: 12px;"><strong>Card contents</strong></p>';
        echo '<img style="-webkit-user-select: none; max-width: 100%; height: auto; border-radius: 8px;" src="idcards.php?name=NFC_Check&amp;mode=' . htmlspecialchars($nfc_check, ENT_QUOTES, 'UTF-8') . '" alt="NFC check">';
    }
    echo '<p style="margin-top: 20px;"><a href="cardmanagement.php?mode=main" class="btn btn-primary">' . arcade_icon('cards') . ' Return to Card Management</a></p>';
    echo '</div></div>';
}

if ($command === '') {
    echo '<div class="alert alert-info" style="max-width: 480px;">No action specified. Use the Card Management screens to run NFC tools.</div>';
    echo '<a href="cardmanagement.php?mode=main" class="btn btn-primary" style="margin-top: 12px;">' . arcade_icon('cards') . ' Card Management</a>';
}

echo '</div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
echo '</body></html>';

?>
