<?php

include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode === 'classic') {
    include 'menu.php';
}

$openjvsmode = file_get_contents('/sbin/piforce/openmode.txt');
$openffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
load_ui_styles();

?>

<body<?php if ($ui_mode === 'modern') echo ' class="kiosk-mode"'; ?>>

<section><center>
    <h1><a href="gamelist.php?display=all">Setup Menu</a><?php ui_mode_indicator(); ?></h1>
    <br>
    
<?php
if ($ui_mode === 'modern') {
    echo '<div class="grid grid-cols-2" style="max-width: 800px; margin: 0 auto;">';
    
    // UI Mode Switcher - First item
    echo '<div class="card card-interactive">';
    echo '<a href="ui-mode-switcher.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🎨</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">UI Mode</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Switch between Classic and Modern UI</p>';
    echo '</div></a></div>';
    
    // Edit Game List
    echo '<div class="card card-interactive">';
    echo '<a href="editgamelist.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">📝</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Edit Game List</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Manage game database</p>';
    echo '</div></a></div>';
    
    // Manage Netdimms
    echo '<div class="card card-interactive">';
    echo '<a href="dimms.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🔌</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Manage NetDIMMs</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Configure network boards</p>';
    echo '</div></a></div>';
} else {
    // Classic mode - original links
    echo '<div class="box2">';
    echo '<a href="ui-mode-switcher.php">🎨 Change UI Mode (Classic/Modern)</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="editgamelist.php">Edit Game List</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="dimms.php">Manage Netdimms</a></div><br>';
}

if ($ui_mode === 'modern') {
    // Update Netdimm Firmware
    echo '<div class="card card-interactive">';
    echo '<a href="fwupdate.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">⬆️</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Update Firmware</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">NetDIMM firmware updates</p>';
    echo '</div></a></div>';
    
    // Card Reader Emulator
    echo '<div class="card card-interactive">';
    echo '<a href="cardemulator.php?mode=main" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">💳</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Card Emulator</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Card reader configuration</p>';
    echo '</div></a></div>';
    
    // Card Data Management
    echo '<div class="card card-interactive">';
    echo '<a href="cardmanagement.php?mode=main" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🗂️</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Card Management</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Manage card data</p>';
    echo '</div></a></div>';
    
    // Import CSV
    echo '<div class="card card-interactive">';
    echo '<a href="importcsv.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">📥</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Import CSV</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Import from boot drive</p>';
    echo '</div></a></div>';
    
    // View CSV Data
    echo '<div class="card card-interactive">';
    echo '<a href="dumpcsv.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">📊</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">View CSV Data</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Raw data viewer</p>';
    echo '</div></a></div>';
} else {
    // Classic mode
    echo '<div class="box2">';
    echo '<a href="fwupdate.php">Update Netdimm Firmware</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="cardemulator.php?mode=main">Card Reader Emulator</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="cardmanagement.php?mode=main">Card Data Management</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="importcsv.php">Import CSV from Boot Drive</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="dumpcsv.php">View CSV Raw Data</a></div><br>';
}
?>
<?php
if ($ui_mode === 'modern') {
    // OpenJVS Configuration
    if ($openjvsmode == 'openon') {
        echo '<div class="card card-interactive">';
        echo '<a href="openjvs.php" style="text-decoration: none; color: inherit;">';
        echo '<div style="padding: var(--space-4);">';
        echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🕹️</div>';
        echo '<h3 style="margin: 0 0 var(--space-2) 0;">OpenJVS</h3>';
        echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Controller configuration</p>';
        echo '</div></a></div>';
    }
    
    // OpenFFB Configuration
    if ($openffbmode == 'ffbon') {
        echo '<div class="card card-interactive">';
        echo '<a href="openffb.php" style="text-decoration: none; color: inherit;">';
        echo '<div style="padding: var(--space-4);">';
        echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🎮</div>';
        echo '<h3 style="margin: 0 0 var(--space-2) 0;">OpenFFB</h3>';
        echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Force feedback setup</p>';
        echo '</div></a></div>';
    }
    
    // Network Configuration
    echo '<div class="card card-interactive">';
    echo '<a href="network.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🌐</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">Network Config</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">WiFi and network settings</p>';
    echo '</div></a></div>';
    
    // Reboot
    echo '<div class="card card-interactive" style="border: 2px solid var(--color-error);">';
    echo '<a href="reboot.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">🔄</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0; color: var(--color-error);">Reboot Pi</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Restart the system</p>';
    echo '</div></a></div>';
    
    echo '</div>'; // Close grid
} else {
    // Classic mode
    if ($openjvsmode == 'openon'){
        echo '<div class="box2">';
        echo '<a href="openjvs.php">OpenJVS Configuration</a></div><br>';
    }
    if ($openffbmode == 'ffbon'){
        echo '<div class="box2">';
        echo '<a href="openffb.php">OpenFFB Configuration</a></div><br>';
    }
    echo '<div class="box2">';
    echo '<a href="network.php">Network Configuration</a></div><br>';
    echo '<div class="box2">';
    echo '<a href="reboot.php">Reboot Raspberry Pi</a><br></div><br>';
}
?>

</center></section>

<?php if ($ui_mode === 'modern'): ?>
    <!-- Modern UI Bottom Nav -->
    <nav class="kiosk-nav">
        <a href="gamelist.php?display=all" class="kiosk-nav-item">
            <span class="kiosk-nav-icon">🎮</span>
            <span class="kiosk-nav-label">Games</span>
        </a>
        <a href="dimms.php" class="kiosk-nav-item">
            <span class="kiosk-nav-icon">🔌</span>
            <span class="kiosk-nav-label">NetDIMMs</span>
        </a>
        <a href="setup.php" class="kiosk-nav-item active">
            <span class="kiosk-nav-icon">⚙️</span>
            <span class="kiosk-nav-label">Setup</span>
        </a>
        <a href="options.php" class="kiosk-nav-item">
            <span class="kiosk-nav-icon">🎛️</span>
            <span class="kiosk-nav-label">Options</span>
        </a>
    </nav>
<?php endif; ?>

</body>
</html>
?>