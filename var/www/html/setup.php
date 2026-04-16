<?php

include 'ui_mode.php';

$openjvsmode = file_get_contents('/sbin/piforce/openmode.txt');
$openffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
load_ui_styles();

?>

<body class="kiosk-mode">
<?php
echo modern_sliding_sidebar_nav('setup');
echo '<div class="container container--narrow pb-24">';
?>

<section><center>
    <h1><a href="gamelist.php?display=all">Setup Menu</a></h1>
    <br>
    
<?php
echo '<div class="grid grid-cols-2" style="max-width: 800px; margin: 0 auto; padding-bottom: 100px;">';

echo '<div class="card card-interactive">';
echo '<a href="editgamelist.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('edit', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Edit Game List</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Manage game database</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive">';
echo '<a href="dimms.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('plug', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Manage NetDIMMs</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Configure network boards</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive">';
echo '<a href="fwupdate.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('arrow-up', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Update Firmware</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">NetDIMM firmware updates</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive">';
echo '<a href="cardemulator.php?mode=main" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('card', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Card Emulator</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Card reader configuration</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive">';
echo '<a href="cardmanagement.php?mode=main" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('cards', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Card Management</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Manage card data</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive">';
echo '<a href="importcsv.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('package', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Import CSV</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Import from boot drive</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive">';
echo '<a href="dumpcsv.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('dashboard', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">View CSV Data</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Raw data viewer</p>';
echo '</div></a></div>';

if ($openjvsmode == 'openon') {
    echo '<div class="card card-interactive">';
    echo '<a href="openjvs.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('gamepad', 'arcade-icon--lg') . '</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">OpenJVS</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Controller configuration</p>';
    echo '</div></a></div>';
}

if ($openffbmode == 'ffbon') {
    echo '<div class="card card-interactive">';
    echo '<a href="openffb.php" style="text-decoration: none; color: inherit;">';
    echo '<div style="padding: var(--space-4);">';
    echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('wheel', 'arcade-icon--lg') . '</div>';
    echo '<h3 style="margin: 0 0 var(--space-2) 0;">OpenFFB</h3>';
    echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Force feedback setup</p>';
    echo '</div></a></div>';
}

echo '<div class="card card-interactive">';
echo '<a href="network.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('globe', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0;">Network Config</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">WiFi and network settings</p>';
echo '</div></a></div>';

echo '<div class="card card-interactive" style="border: 2px solid var(--color-error);">';
echo '<a href="reboot.php" style="text-decoration: none; color: inherit;">';
echo '<div style="padding: var(--space-4);">';
echo '<div style="margin-bottom: var(--space-2);">' . arcade_icon('refresh', 'arcade-icon--lg') . '</div>';
echo '<h3 style="margin: 0 0 var(--space-2) 0; color: var(--color-error);">Reboot Pi</h3>';
echo '<p style="color: var(--color-text-secondary); font-size: var(--font-size-sm); margin: 0;">Restart the system</p>';
echo '</div></a></div>';

echo '</div>'; // Close grid
?>

</center></section>
<?php
echo '</div>';
echo '</div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
?>

</body>
</html>
