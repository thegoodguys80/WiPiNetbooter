<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Main Menu</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header"><h2>WiPi Netbooter</h2></div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item active"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container"><div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()"><span></span><span></span><span></span></button>';
    echo '<h1>🏠 Main Menu</h1>';
    echo '<p style="margin-bottom: 32px; color: #aaa;">Welcome to WiPi Netbooter - Your Sega arcade netbooting solution</p>';
    
    // Games Section
    echo '<h2 style="margin-bottom: 16px; color: #4a9eff;">🎮 Games</h2>';
    echo '<div class="grid grid-cols-3" style="margin-bottom: 32px;">';
    
    echo '<a href="gamelist.php?display=all" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">📚 Game Library</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Browse and launch all available games</p>';
    echo '</div></a>';
    
    echo '<a href="gamelist.php?display=faves" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">⭐ Favourites</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Quick access to your favorite games</p>';
    echo '</div></a>';
    
    echo '<a href="editgamelist.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">✏️ Manage Games</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Enable/disable games in your library</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // NetDIMM Section
    echo '<h2 style="margin-bottom: 16px; color: #4a9eff;">🖥️ NetDIMM</h2>';
    echo '<div class="grid grid-cols-2" style="margin-bottom: 32px;">';
    
    echo '<a href="dimms.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">🌐 NetDIMM Manager</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Configure and manage your NetDIMM boards</p>';
    echo '</div></a>';
    
    echo '<a href="dimmscanner.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">🔍 Scan Network</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Discover NetDIMM boards on your network</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // Setup & Configuration Section
    echo '<h2 style="margin-bottom: 16px; color: #4a9eff;">⚙️ Setup & Configuration</h2>';
    echo '<div class="grid grid-cols-3" style="margin-bottom: 32px;">';
    
    echo '<a href="options.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">🎛️ Options</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">System settings and preferences</p>';
    echo '</div></a>';
    
    echo '<a href="network.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">📡 Network</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Network configuration and status</p>';
    echo '</div></a>';
    
    echo '<a href="openjvs.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">🕹️ OpenJVS</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Controller and input configuration</p>';
    echo '</div></a>';
    
    echo '<a href="openffb.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">🎯 Force Feedback</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Force feedback wheel configuration</p>';
    echo '</div></a>';
    
    echo '<a href="cardemulator.php?mode=main" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">💳 Card Emulator</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Arcade card reader emulation</p>';
    echo '</div></a>';
    
    echo '<a href="cardmanagement.php?mode=main" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">📇 Card Management</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Manage saved card data</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // Tools & Utilities Section
    echo '<h2 style="margin-bottom: 16px; color: #4a9eff;">🔧 Tools & Utilities</h2>';
    echo '<div class="grid grid-cols-3" style="margin-bottom: 32px;">';
    
    echo '<a href="help.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">❓ Help</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Documentation and guides</p>';
    echo '</div></a>';
    
    echo '<a href="dumpcsv.php" class="card card-interactive" style="text-decoration: none;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px;">💾 ROM Database</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">View ROM information database</p>';
    echo '</div></a>';
    
    echo '<a href="shutdown.php" class="card card-interactive" style="text-decoration: none; background: #3a2a2a;">';
    echo '<div class="card-body">';
    echo '<h3 style="margin-bottom: 8px; color: #ff6b6b;">🔌 Shutdown</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Power off the system safely</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // UI Mode Switcher
    echo '<div class="card" style="background: #2a2a2a; border: 1px solid #444;">';
    echo '<div class="card-body">';
    echo '<div style="display: flex; justify-content: space-between; align-items: center;">';
    echo '<div>';
    echo '<h3 style="margin-bottom: 4px;">🎨 UI Mode</h3>';
    echo '<p style="color: #aaa; font-size: 0.9em;">Currently using Modern UI</p>';
    echo '</div>';
    echo '<a href="ui-mode-switcher.php?mode=classic&return=menu.php" class="btn btn-secondary" style="text-decoration: none;">Switch to Classic</a>';
    echo '</div></div></div>';
    
    echo '</div></div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}</script>';
    echo '</body></html>';
} else {
    // Classic UI - simple menu list
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<meta name="description" content="Responsive Header Nav">';
    echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    include 'menu_include.php';
    echo '<section><center><p>';
    echo '<h1>Main Menu</h1><br>';
    
    echo '<h2>Games</h2>';
    echo '<a href="gamelist.php?display=all" class="dropbtn">Game Library</a><br><br>';
    echo '<a href="gamelist.php?display=faves" class="dropbtn">Favourites</a><br><br>';
    echo '<a href="editgamelist.php" class="dropbtn">Manage Games</a><br><br><br>';
    
    echo '<h2>NetDIMM</h2>';
    echo '<a href="dimms.php" class="dropbtn">NetDIMM Manager</a><br><br>';
    echo '<a href="dimmscanner.php" class="dropbtn">Scan Network</a><br><br><br>';
    
    echo '<h2>Setup & Configuration</h2>';
    echo '<a href="options.php" class="dropbtn">Options</a><br><br>';
    echo '<a href="network.php" class="dropbtn">Network</a><br><br>';
    echo '<a href="openjvs.php" class="dropbtn">OpenJVS</a><br><br>';
    echo '<a href="openffb.php" class="dropbtn">Force Feedback</a><br><br>';
    echo '<a href="cardemulator.php?mode=main" class="dropbtn">Card Emulator</a><br><br>';
    echo '<a href="cardmanagement.php?mode=main" class="dropbtn">Card Management</a><br><br><br>';
    
    echo '<h2>Tools & Utilities</h2>';
    echo '<a href="help.php" class="dropbtn">Help</a><br><br>';
    echo '<a href="dumpcsv.php" class="dropbtn">ROM Database</a><br><br>';
    echo '<a href="shutdown.php" class="dropbtn">Shutdown</a><br><br>';
    
    echo '</p></center></section></body></html>';
}
?>
