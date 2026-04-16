<?php
include 'ui_mode.php';

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - OpenFFB</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container">';
    
    echo '<h1>'.arcade_icon('wheel').' OpenFFB Configuration</h1>';
    echo '<p style="margin-bottom: 24px;">Configure force feedback support for racing games</p>';
    
    echo '<div class="grid grid-cols-2">';
    
    // Mapping Configuration
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('list').' FFB Mappings</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Manage force feedback device mappings</p>';
    echo '<a href="ffbmapping.php" class="btn btn-primary btn-block">'.arcade_icon('edit').' Manage Mapping Files</a>';
    echo '<a href="editffbmappings.php" class="btn btn-secondary btn-block" style="margin-top: 8px;">'.arcade_icon('edit').' Update Game Mappings</a>';
    echo '</div></div>';
    
    // System Control
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('setup').' System Control</h3></div>';
    echo '<div class="card-body">';
    echo '<p style="margin-bottom: 16px;">OpenFFB system management</p>';
    echo '<a href="updateopenffb.php" class="btn btn-primary btn-block">'.arcade_icon('refresh').' Update OpenFFB</a>';
    echo '</div></div>';
    
    echo '</div>';
    
    echo '</div></div>'; // Close main-content and container
    
    // Add sidebar toggle script
    echo '<script>';
    echo 'function toggleSidebar() {';
    echo '  const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");';
    echo '  if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");';
    echo '}';
    echo '</script>';
    echo '</body></html>';
?>
