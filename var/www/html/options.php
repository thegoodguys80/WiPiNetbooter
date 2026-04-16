<?php

include_once 'ui_mode.php';

$powermode = file_get_contents('/sbin/piforce/powerfile.txt');
$bootmode = file_get_contents('/sbin/piforce/bootfile.txt');
$bootrom = @file_get_contents('/var/www/logs/log.txt'); // Use @ to suppress warning if file doesn't exist
$menumode = file_get_contents('/sbin/piforce/menumode.txt');
$relaymode = file_get_contents('/sbin/piforce/relaymode.txt');
$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$zeromode = file_get_contents('/sbin/piforce/zeromode.txt');
$openmode = file_get_contents('/sbin/piforce/openmode.txt');
$soundmode = file_get_contents('/sbin/piforce/soundmode.txt');
$navmode = file_get_contents('/sbin/piforce/navmode.txt');
$openffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');
$emumode = file_get_contents('/sbin/piforce/emumode.txt');
$nfcmode = file_get_contents('/sbin/piforce/nfcmode.txt');

$csvfile = 'csv/romsinfo.csv';
$path = '/boot/roms';

$lastgamearray = explode(" ", $bootrom);
$lastgame = $lastgamearray[0];

$f = fopen($csvfile, "r");
 while ($row = fgetcsv($f)) {
   if ($row[1] == $lastgame){
     $gamename = $row[4];
   }
}

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="System Options">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';

load_ui_styles();
?>

<!DOCTYPE html>
<html>
<body>

<?php

echo modern_sliding_sidebar_nav('options');
echo '<div class="container" style="padding: 20px;">';
echo '<h1 class="text-3xl" style="margin-bottom: 24px;">'.arcade_icon('cabinet').' System Options</h1>';
echo '<div class="grid grid-cols-2" style="margin-bottom: 32px;">';

// Helper function to render option card
function render_option_card($title, $description, $is_enabled, $enable_url, $disable_url, $icon = 'setup') {
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center;">';
    echo '<div style="flex: 1;">';
    echo '<div style="margin-bottom: 8px;">'.arcade_icon($icon, 'arcade-icon--lg').'</div>';
    echo '<h3 style="margin: 0 0 4px 0;">'.$title.'</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: 14px; margin: 0;">'.$description.'</p>';
    echo '</div>';
    echo '<div style="display: flex; gap: 8px; align-items: center;">';
    if ($is_enabled) {
        echo '<span class="badge badge-success">✓ Enabled</span>';
        echo '<a href="'.$disable_url.'" class="btn btn-secondary btn-sm">Disable</a>';
    } else {
        echo '<span class="badge" style="background: var(--color-surface-hover);">✕ Disabled</span>';
        echo '<a href="'.$enable_url.'" class="btn btn-primary btn-sm">Enable</a>';
    }
    echo '</div></div></div></div>';
}

    render_option_card('Simple Menu', 'Launch games directly without detail view', $menumode == 'simple', 'switchmode.php?mode=simple', 'switchmode.php?mode=advanced', 'list');
    render_option_card('Power Saver', 'Auto power off when inactive', $powermode == 'auto-off', 'switchmode.php?mode=auto-off', 'switchmode.php?mode=always-on', 'battery');
    render_option_card('Single Boot', 'Auto-boot last played game', $bootmode == 'single', 'switchmode.php?mode=single', 'switchmode.php?mode=multi', 'rocket');
    render_option_card('Relay Reboot', 'Hardware relay for board reset', $relaymode == 'relayon', 'switchmode.php?mode=relayon', 'switchmode.php?mode=relayoff', 'lightning');
    render_option_card('Time Hack', 'Zero security mode for compatibility', $zeromode == 'hackon', 'switchmode.php?mode=hackon', 'switchmode.php?mode=hackoff', 'clock');
    render_option_card('Video Sound', 'Enable audio in game preview videos', $soundmode == 'soundon', 'switchmode.php?mode=soundon', 'switchmode.php?mode=soundoff', 'speaker');
    render_option_card('Nav Button', 'Show return to top button', $navmode == 'navon', 'switchmode.php?mode=navon', 'switchmode.php?mode=navoff', 'arrow-up');
    render_option_card('OpenJVS', 'USB controller support', $openmode == 'openon', 'switchmode.php?mode=openon', 'switchmode.php?mode=openoff', 'gamepad');
    render_option_card('OpenFFB', 'Force feedback support', $openffbmode == 'ffbon', 'switchmode.php?mode=ffbon', 'switchmode.php?mode=ffboff', 'wheel');
    render_option_card('NFC Support', 'NFC card reader integration', $nfcmode == 'nfcon', 'switchmode.php?mode=nfcon', 'switchmode.php?mode=nfcoff', 'nfc');
    
    // LCD Mode - special handling
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center;">';
    echo '<div style="flex: 1;">';
    echo '<div style="margin-bottom: 8px;">'.arcade_icon('tv', 'arcade-icon--lg').'</div>';
    echo '<h3 style="margin: 0 0 4px 0;">LCD Mode</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: 14px; margin: 0;">Display type configuration</p>';
    echo '</div>';
    echo '<div style="display: flex; gap: 8px; align-items: center;">';
    if ($lcdmode == 'LCD16') {
        echo '<span class="badge badge-primary">16x2 Display</span>';
        echo '<a href="switchmode.php?mode=LCD35" class="btn btn-secondary btn-sm">Switch to 3.5" Touch</a>';
    } else {
        echo '<span class="badge badge-primary">3.5" Touch</span>';
        echo '<a href="switchmode.php?mode=LCD16" class="btn btn-secondary btn-sm">Switch to 16x2</a>';
    }
    echo '</div></div></div></div>';
    
    // Card Emulator Mode - special handling
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center;">';
    echo '<div style="flex: 1;">';
    echo '<div style="margin-bottom: 8px;">'.arcade_icon('cards', 'arcade-icon--lg').'</div>';
    echo '<h3 style="margin: 0 0 4px 0;">Card Emu Mode</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: 14px; margin: 0;">Auto-launch card emulator</p>';
    echo '</div>';
    echo '<div style="display: flex; gap: 8px; align-items: center;">';
    if ($emumode == 'auto') {
        echo '<span class="badge badge-success">Auto</span>';
        echo '<a href="switchmode.php?mode=manual" class="btn btn-secondary btn-sm">Switch to Manual</a>';
    } else {
        echo '<span class="badge badge-primary">Manual</span>';
        echo '<a href="switchmode.php?mode=auto" class="btn btn-secondary btn-sm">Switch to Auto</a>';
    }
    echo '</div></div></div></div>';
    
    echo '</div>'; // Close grid
    
    // Last game played card
    echo '<div class="card" style="max-width: 600px;">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('lastgame').' Last Game Played</h3></div>';
    echo '<div class="card-body">';
    if ($lastgame !== '' && isset($gamename)) {
        echo '<p style="font-size: 18px; margin: 0;">'.$gamename.'</p>';
    } else {
        echo '<p style="color: var(--color-text-secondary); margin: 0;">No games played yet</p>';
    }
    echo '</div></div>';
    
    echo '</div></div>'; // Close main-content and container
    
    // Add sidebar toggle script
    echo '<script>';
    echo 'function toggleSidebar() {';
    echo '  const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");';
    echo '  if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");';
    echo '}';
    echo '</script>';
?>

</body>
</html>
