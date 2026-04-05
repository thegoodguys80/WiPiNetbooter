<?php
include_once 'ui_mode.php';

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - ROM Audit</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('scan') . ' ROM File Audit</h1>';
    echo '<p class="page-intro">Scan your ROM files, identify them, and update your game list.</p>';

    echo '<div class="card" style="max-width:640px;">';
    echo '<div class="card-header"><h2 class="card-title">How It Works</h2></div>';
    echo '<div class="card-body">';
    echo '<div style="display:flex;flex-direction:column;gap:12px;">';
    echo '<div class="alert alert-info"><strong>1. ROM Audit Scan</strong> — Identifies your ROMs using the file header.</div>';
    echo '<div class="alert alert-info"><strong>2. ROM Audit Results</strong> — Displays the scan results for review.</div>';
    echo '<div class="alert alert-info"><strong>3. ROM Audit Save</strong> — Writes audit results into the game list using your file names.</div>';
    echo '<div class="alert alert-info"><strong>4. ROM Audit Rename</strong> — Saves to game list and renames your ROM files to a standard format.</div>';
    echo '</div>';
    echo '</div>';
    echo '<div class="card-footer">';
    echo '<a href="romaudit.php" class="btn btn-primary">' . arcade_icon('lightning') . ' Start Audit Scan</a>';
    echo '<a href="setup.php" class="btn btn-secondary" style="margin-left:8px;">Cancel</a>';
    echo '</div></div>';

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
