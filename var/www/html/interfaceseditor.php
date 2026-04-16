<?php
include_once 'ui_mode.php';

$interfacesfile = '/etc/network/interfaces';

if (isset($_POST['text'])) {
    file_put_contents($interfacesfile, $_POST['text']);
    header('Location: interfaceseditor.php');
    exit;
}

$text = file_exists($interfacesfile) ? file_get_contents($interfacesfile) : '';


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Interfaces Editor</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '<style>textarea{width:100%;height:24em;font-family:monospace;font-size:13px;background:var(--color-surface-hover,#1a1a1a);color:var(--color-text-primary,#f0f0f0);border:1px solid var(--color-border,#333);border-radius:6px;padding:12px;resize:vertical;}</style>';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('network');
    echo '<div class="container p-6">';
    echo '<h1>&#9881; Interfaces File Editor</h1>';
    echo '<p class="page-intro">Edit <code>/etc/network/interfaces</code> directly.</p>';
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<form action="" method="post">';
    echo '<textarea name="text">' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</textarea>';
    echo '<div style="margin-top:16px;display:flex;gap:8px;">';
    echo '<input type="submit" class="btn btn-primary" value="Save File">';
    echo '<input type="reset" class="btn btn-secondary" value="Reset">';
    echo '<a href="network.php" class="btn btn-secondary">Cancel</a>';
    echo '</div></form>';
    echo '</div></div>';
    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
