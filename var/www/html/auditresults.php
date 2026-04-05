<?php
include_once 'ui_mode.php';
include 'auditscanresults.php';


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Audit Results</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('scan') . ' ROM Audit Results</h1>';

    // Successes
    echo '<div class="card" style="margin-bottom:24px;">';
    echo '<div class="card-header"><h2 class="card-title"><span class="badge badge-success">' . intval($successes) . '</span> Files Successfully Identified</h2></div>';
    echo '<div class="card-body">';
    if ($successes > 0) {
        echo '<table class="table" style="width:100%"><thead><tr><th>ROM Filename</th><th>Game Name</th></tr></thead><tbody>';
        for ($i = 1; $i <= $successes; $i++) {
            echo '<tr><td>' . htmlspecialchars(${'filename'.$i}, ENT_QUOTES, 'UTF-8') . '</td><td style="color:var(--color-success,#22c55e);">' . htmlspecialchars(${'gamename'.$i}, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p class="text-secondary">No files identified.</p>';
    }
    echo '</div></div>';

    if ($duplicates > 0) {
        echo '<div class="card" style="margin-bottom:24px;">';
        echo '<div class="card-header"><h2 class="card-title"><span class="badge badge-primary">' . intval($duplicates) . '</span> Duplicate Files Detected</h2></div>';
        echo '<div class="card-body">';
        echo '<table class="table" style="width:100%"><thead><tr><th>ROM Filename</th></tr></thead><tbody>';
        for ($i = 1; $i <= $duplicates; $i++) {
            echo '<tr><td style="color:var(--color-accent,#7c3aed);">' . htmlspecialchars(${'duplicate'.$i}, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '</div></div>';
    }

    if ($failures > 0) {
        echo '<div class="card" style="margin-bottom:24px;">';
        echo '<div class="card-header"><h2 class="card-title"><span class="badge badge-warning">' . intval($failures) . '</span> Audit Failures</h2></div>';
        echo '<div class="card-body">';
        echo '<table class="table" style="width:100%"><thead><tr><th>ROM Filename</th></tr></thead><tbody>';
        for ($i = 1; $i <= $failures; $i++) {
            echo '<tr><td style="color:var(--color-warning,#f59e0b);">' . htmlspecialchars(${'failure'.$i}, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '</div></div>';
    }

    echo '<div style="display:flex;gap:12px;flex-wrap:wrap;">';
    echo '<a href="saveaudit.php?rename=no" class="btn btn-primary">' . arcade_icon('package') . ' Save Audit Results</a>';
    echo '<a href="saveaudit.php?rename=yes" class="btn btn-secondary">Save &amp; Rename Files</a>';
    echo '</div>';

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
