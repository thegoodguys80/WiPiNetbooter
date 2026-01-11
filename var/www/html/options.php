<?php

// Load UI mode helper
include_once 'ui_mode.php';
$ui_mode = get_ui_mode();

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
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';

// Load CSS based on UI mode
load_ui_styles();

// Only include old menu for classic UI
if ($ui_mode === 'classic') {
    include 'menu_include.php';
}
?>

<!DOCTYPE html>
<html>
<body>

<?php

// Modern UI wrapper
if ($ui_mode === 'modern') {
    echo '<button class="burger-menu" id="burgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu"><span></span><span></span><span></span></button>';
    echo '<div class="sidebar-nav" id="sidebarNav">';
    echo '<nav>';
    echo '<a href="gamelist.php?display=all" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎮</span><span class="sidebar-nav-label">Games</span></a>';
    echo '<a href="dimms.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">💾</span><span class="sidebar-nav-label">NetDIMMs</span></a>';
    echo '<a href="setup.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">⚙️</span><span class="sidebar-nav-label">Setup</span></a>';
    echo '<a href="options.php" class="sidebar-nav-item active">';
    echo '<span class="sidebar-nav-icon">🏛️</span><span class="sidebar-nav-label">Options</span></a>';
    echo '<a href="ui-mode-switcher.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎨</span><span class="sidebar-nav-label">UI Mode</span></a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    echo '<div class="main-content">';
    echo '<div class="container" style="padding: 20px;">';
    echo '<h1 class="text-3xl" style="margin-bottom: 24px;">🏛️ System Options</h1>';
    echo '<div class="grid grid-cols-2" style="margin-bottom: 32px;">';
} else {
    echo '<section><center>';
    echo '<h1><a href="gamelist.php?display=all">Options Menu</a></h1><br>';
    echo '<html><body><table class="center" id="options"><tr><th>Option</th><th>Setting</th><th>Action</th></tr>';
}

// Helper function to render option card in modern UI
function render_option_card($title, $description, $is_enabled, $enable_url, $disable_url, $icon = '⚙️') {
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center;">';
    echo '<div style="flex: 1;">';
    echo '<div style="font-size: 24px; margin-bottom: 8px;">'.$icon.'</div>';
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

// Render options based on UI mode
if ($ui_mode === 'modern') {
    // Modern UI cards
    render_option_card('Simple Menu', 'Launch games directly without detail view', $menumode == 'simple', 'switchmode.php?mode=simple', 'switchmode.php?mode=advanced', '📝');
    render_option_card('Power Saver', 'Auto power off when inactive', $powermode == 'auto-off', 'switchmode.php?mode=auto-off', 'switchmode.php?mode=always-on', '🔋');
    render_option_card('Single Boot', 'Auto-boot last played game', $bootmode == 'single', 'switchmode.php?mode=single', 'switchmode.php?mode=multi', '🚀');
    render_option_card('Relay Reboot', 'Hardware relay for board reset', $relaymode == 'relayon', 'switchmode.php?mode=relayon', 'switchmode.php?mode=relayoff', '🔄');
    render_option_card('Time Hack', 'Zero security mode for compatibility', $zeromode == 'hackon', 'switchmode.php?mode=hackon', 'switchmode.php?mode=hackoff', '⏱️');
    render_option_card('Video Sound', 'Enable audio in game preview videos', $soundmode == 'soundon', 'switchmode.php?mode=soundon', 'switchmode.php?mode=soundoff', '🔊');
    render_option_card('Nav Button', 'Show return to top button', $navmode == 'navon', 'switchmode.php?mode=navon', 'switchmode.php?mode=navoff', '⬆️');
    render_option_card('OpenJVS', 'USB controller support', $openmode == 'openon', 'switchmode.php?mode=openon', 'switchmode.php?mode=openoff', '🎮');
    render_option_card('OpenFFB', 'Force feedback support', $openffbmode == 'ffbon', 'switchmode.php?mode=ffbon', 'switchmode.php?mode=ffboff', '🏏');
    render_option_card('NFC Support', 'NFC card reader integration', $nfcmode == 'nfcon', 'switchmode.php?mode=nfcon', 'switchmode.php?mode=nfcoff', '📳');
    
    // LCD Mode - special handling
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center;">';
    echo '<div style="flex: 1;">';
    echo '<div style="font-size: 24px; margin-bottom: 8px;">📺</div>';
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
    echo '<div style="font-size: 24px; margin-bottom: 8px;">🎴</div>';
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
    echo '<div class="card-header"><h3 class="card-title">🎮 Last Game Played</h3></div>';
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
    echo '  const sidebar = document.getElementById("sidebarNav");';
    echo '  const overlay = document.getElementById("sidebarOverlay");';
    echo '  const burger = document.getElementById("burgerBtn");';
    echo '  sidebar.classList.toggle("open");';
    echo '  overlay.classList.toggle("show");';
    echo '  burger.classList.toggle("open");';
    echo '}';
    echo '</script>';
} else {
    // Classic UI table rows
    if ($menumode == 'simple'){echo '<tr><td>Simple Menu</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=advanced">disable</a></td></tr>';}
    if ($menumode == 'advanced'){echo '<tr><td>Simple Menu</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=simple">enable</a></td></tr>';}
    if ($powermode == 'always-on'){echo '<tr><td>Power Saver</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=auto-off">enable</a></td></tr>';}
    if ($powermode == 'auto-off'){echo '<tr><td>Power Saver</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=always-on">disable</a></td></tr>';}
    if ($bootmode == 'multi'){echo '<tr><td>Single Boot</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=single">enable</a></td></tr>';}
    if ($bootmode == 'single'){echo '<tr><td>Single Boot</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=multi">disable</a></td></tr>';}
    if ($relaymode == 'relayon'){echo '<tr><td>Relay Reboot</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=relayoff">disable</a></td></tr>';}
    if ($relaymode == 'relayoff'){echo '<tr><td>Relay Reboot</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=relayon">enable</a></td></tr>';}
    if ($zeromode == 'hackon'){echo '<tr><td>Time Hack</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=hackoff">disable</a></td></tr>';}
    if ($zeromode == 'hackoff'){echo '<tr><td>Time Hack</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=hackon">enable</a></td></tr>';}
    if ($soundmode == 'soundon'){echo '<tr><td>Video Sound</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=soundoff">disable</a></td></tr>';}
    if ($soundmode == 'soundoff'){echo '<tr><td>Video Sound</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=soundon">enable</a></td></tr>';}
    if ($navmode == 'navon'){echo '<tr><td>Nav Button</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=navoff">disable</a></td></tr>';}
    if ($navmode == 'navoff'){echo '<tr><td>Nav Button</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=navon">enable</a></td></tr>';}
    if ($openmode == 'openon'){echo '<tr><td>OpenJVS</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=openoff">disable</a></td></tr>';}
    if ($openmode == 'openoff'){echo '<tr><td>OpenJVS</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=openon">enable</a></td></tr>';}
    if ($openffbmode == 'ffbon'){echo '<tr><td>OpenFFB</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=ffboff">disable</a></td></tr>';}
    if ($openffbmode == 'ffboff'){echo '<tr><td>OpenFFB</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=ffbon">enable</a></td></tr>';}
    if ($nfcmode == 'nfcon'){echo '<tr><td>NFC Support</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=nfcoff">disable</a></td></tr>';}
    if ($nfcmode == 'nfcoff'){echo '<tr><td>NFC Support</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=nfcon">enable</a></td></tr>';}
    if ($lcdmode == 'LCD16'){echo '<tr><td>LCD Mode</td><td><b>16x2</b></td><td><a href="switchmode.php?mode=LCD35">3.5 touch</a></td></tr>';}
    if ($lcdmode == 'LCD35'){echo '<tr><td>LCD Mode</td><td><b>3.5 touch</b></td><td><a href="switchmode.php?mode=LCD16">16x2</a></td></tr>';}
    if ($emumode == 'manual'){echo '<tr><td>Card Emu Mode</td><td><b>manual</b></td><td><a href="switchmode.php?mode=auto">auto</a></td></tr></table>';}
    if ($emumode == 'auto'){echo '<tr><td>Card Emu Mode</td><td><b>auto</b></td><td><a href="switchmode.php?mode=manual">manual</a></td></tr></table>';}
    
    echo '<table class="center" id="options"><tr></tr>';
    if ($lastgame !== ''){echo '<tr><td><b>Last Game Played: </td><td>'.$gamename.'</td></tr></table>';}
    else {echo '<tr><td><b>Last Game Played: </td><td>Unknown</td></tr></table>';}
    echo '</html>';
}
?>

</body>
</html>
