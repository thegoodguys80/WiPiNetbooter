<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu_include.php';
}

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - OpenFFB</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    // Sidebar navigation
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header">';
    echo '<h2>WiPi Netbooter</h2>';
    echo '</div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
    <a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item active"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav>';
    echo '</div>';
    
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container">';
    echo '<div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()">';
    echo '<span></span><span></span><span></span>';
    echo '</button>';
    
    echo '<h1>🎮 OpenFFB Configuration</h1>';
    echo '<p style="margin-bottom: 24px;">Configure force feedback support for racing games</p>';
    
    echo '<div class="grid grid-cols-2">';
    
    // Mapping Configuration
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">🗺️ FFB Mappings</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Manage force feedback device mappings</p>';
    echo '<a href="ffbmapping.php" class="btn btn-primary btn-block">📝 Manage Mapping Files</a>';
    echo '<a href="editffbmappings.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">✏️ Update Game Mappings</a>';
    echo '</div></div>';
    
    // System Control
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">⚙️ System Control</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">OpenFFB system management</p>';
    echo '<a href="updateopenffb.php" class="btn btn-primary btn-block">🔄 Update OpenFFB</a>';
    echo '</div></div>';
    
    echo '</div>';
    
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
    echo '</body></html>';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center><p>';
    echo '<h1><a href="setup.php">OpenFFB Configuration Menu</a></h1><br>';
    echo '<div class="box2"><a href="ffbmapping.php">Manage Mapping Files</a><br></div><br>';
    echo '<div class="box2"><a href="editffbmappings.php">Update Game Mappings</a><br></div><br>';
    echo '<div class="box2"><a href="updateopenffb.php">Update OpenFFB</a><br></div><br>';
    echo '</p><center></body></html>';
}
?>
