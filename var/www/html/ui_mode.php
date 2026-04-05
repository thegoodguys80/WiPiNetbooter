<?php
/**
 * UI helpers — modern theme only (sidebar, components, kiosk layout).
 */

/**
 * Inline arcade SVG icons (see css/arcade-icons.css). $name must match .arcade-icon--{name}.
 */
function arcade_icon($name, $extra_class = '') {
    static $allowed = [
        'dashboard', 'games', 'netdimms', 'setup', 'menu', 'options', 'network', 'uimode',
        'home', 'library', 'favorites', 'edit', 'scan', 'globe', 'gamepad', 'wheel',
        'card', 'cards', 'help', 'romdb', 'power', 'tools', 'palette', 'coin', 'lastgame',
        'cabinet', 'system', 'battery', 'rocket', 'refresh', 'clock', 'speaker', 'arrow-up',
        'nfc', 'tv', 'list', 'lightning', 'plug', 'trash', 'bluetooth', 'warning', 'package',
    ];
    $n = in_array($name, $allowed, true) ? $name : 'games';
    $c = 'arcade-icon arcade-icon--' . $n;
    if ($extra_class !== '') {
        $c .= ' ' . $extra_class;
    }
    return '<span class="' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></span>';
}

function arcade_nav_icon($name) {
    return arcade_icon($name, 'nav-icon');
}

function arcade_sidebar_icon($name) {
    return arcade_icon($name, 'sidebar-nav-icon');
}

function arcade_kiosk_nav_icon($name) {
    return arcade_icon($name, 'kiosk-nav-icon');
}

/**
 * Standard sliding sidebar (matches kiosk-mode.css .sidebar-nav#sidebarNav).
 *
 * @param string $active One of: dashboard, games, netdimms, setup, options, network, cards, help, shutdown
 */
function modern_sliding_sidebar_nav($active = '') {
    // Read configured NetDIMMs for sidebar status
    $dimms_sidebar = [];
    $dimms_csv = __DIR__ . '/csv/dimms.csv';
    if (file_exists($dimms_csv)) {
        $fh = fopen($dimms_csv, 'r');
        fgetcsv($fh); // skip header
        while (($row = fgetcsv($fh)) !== false) {
            if (!empty($row[1]) && filter_var(trim($row[1]), FILTER_VALIDATE_IP)) {
                $dimms_sidebar[] = trim($row[1]);
            }
        }
        fclose($fh);
    }
    $dimm_count = count($dimms_sidebar);

    $main_items = [
        ['key' => 'dashboard', 'href' => 'menu.php',                    'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['key' => 'games',     'href' => 'gamelist.php?display=all',     'icon' => 'games',     'label' => 'Games'],
        ['key' => 'netdimms',  'href' => 'dimms.php',                   'icon' => 'netdimms',  'label' => 'NetDIMMs'],
        ['key' => 'setup',     'href' => 'setup.php',                   'icon' => 'setup',     'label' => 'Setup'],
        ['key' => 'options',   'href' => 'options.php',                 'icon' => 'cabinet',   'label' => 'Options'],
        ['key' => 'network',   'href' => 'network.php',                 'icon' => 'network',   'label' => 'Network'],
        ['key' => 'cards',     'href' => 'cardemulator.php?mode=main',  'icon' => 'card',      'label' => 'Card Emulator'],
    ];
    $util_items = [
        ['key' => 'help',     'href' => 'help.php',     'icon' => 'help',  'label' => 'Help',     'danger' => false],
        ['key' => 'shutdown', 'href' => 'shutdown.php', 'icon' => 'power', 'label' => 'Shutdown', 'danger' => true],
    ];

    $out = '<button class="burger-menu" id="burgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu"><span></span><span></span><span></span></button>';
    $out .= '<div class="sidebar-nav" id="sidebarNav"><nav>';

    foreach ($main_items as $it) {
        $cls = 'sidebar-nav-item' . ($active === $it['key'] ? ' active' : '');
        $out .= '<a href="' . htmlspecialchars($it['href'], ENT_QUOTES, 'UTF-8') . '" class="' . $cls . '">';
        $out .= arcade_sidebar_icon($it['icon']);
        $out .= '<span class="sidebar-nav-label">' . htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8') . '</span></a>';
    }

    $out .= '<div class="sidebar-divider"></div>';

    foreach ($util_items as $it) {
        $cls = 'sidebar-nav-item' . ($active === $it['key'] ? ' active' : '') . ($it['danger'] ? ' sidebar-nav-item--danger' : '');
        $out .= '<a href="' . htmlspecialchars($it['href'], ENT_QUOTES, 'UTF-8') . '" class="' . $cls . '">';
        $out .= arcade_sidebar_icon($it['icon']);
        $out .= '<span class="sidebar-nav-label">' . htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8') . '</span></a>';
    }

    // Footer: live status + theme toggle
    $out .= '<div class="sidebar-footer">';

    // WiFi status (populated async)
    $out .= '<div class="sidebar-status">';
    $out .= arcade_sidebar_icon('network');
    $out .= '<span class="sidebar-nav-label sidebar-status__text" id="sidebarWifiLabel">WiFi…</span>';
    $out .= '</div>';

    // NetDIMM compact status (if any configured)
    if ($dimm_count > 0) {
        $out .= '<div class="sidebar-status">';
        $out .= arcade_sidebar_icon('netdimms');
        $out .= '<span class="sidebar-nav-label sidebar-status__text"><span id="sidebarDimmOnline">–</span>/' . $dimm_count . ' NetDIMM</span>';
        $out .= '</div>';
    }

    // Theme toggle
    $out .= '<button class="sidebar-theme-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Toggle dark/light mode" aria-label="Toggle theme">';
    $out .= '<span id="themeIcon">◑</span>';
    $out .= '<span class="sidebar-nav-label" id="themeLabel">Theme</span>';
    $out .= '</button>';
    $out .= '</div>'; // sidebar-footer

    $out .= '</nav></div>';
    $out .= '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    $out .= '<div class="main-content">';

    // JS: theme toggle + async status checks
    $out .= '<script>';
    $out .= 'function toggleTheme(){';
    $out .= '  var cur=localStorage.getItem("wipi-theme")||"dark";';
    $out .= '  var next=cur==="dark"?"light":"dark";';
    $out .= '  document.documentElement.setAttribute("data-theme",next);';
    $out .= '  localStorage.setItem("wipi-theme",next);';
    $out .= '  _updateThemeIcon(next);';
    $out .= '}';
    $out .= 'function _updateThemeIcon(t){';
    $out .= '  var i=document.getElementById("themeIcon");';
    $out .= '  var l=document.getElementById("themeLabel");';
    $out .= '  if(i)i.textContent=t==="dark"?"☀":"◑";';
    $out .= '  if(l)l.textContent=t==="dark"?"Light mode":"Dark mode";';
    $out .= '}';
    $out .= '(function(){';
    $out .= '  var t=localStorage.getItem("wipi-theme")||"dark";';
    $out .= '  document.documentElement.setAttribute("data-theme",t);';
    $out .= '  document.addEventListener("DOMContentLoaded",function(){_updateThemeIcon(t);});';
    $out .= '})();';
    // WiFi status async
    $out .= '(function(){fetch("wifistatus.php").then(function(r){return r.json();}).then(function(d){';
    $out .= '  var el=document.getElementById("sidebarWifiLabel");';
    $out .= '  if(el)el.textContent=d.ssid?d.ssid:"No WiFi";';
    $out .= '}).catch(function(){var el=document.getElementById("sidebarWifiLabel");if(el)el.textContent="WiFi?";});})();';
    // NetDIMM online count async
    if ($dimm_count > 0) {
        $dimms_js = json_encode($dimms_sidebar);
        $out .= '(function(){var ips='.$dimms_js.';var online=0;';
        $out .= 'ips.forEach(function(ip){fetch("pingtest.php?ip="+encodeURIComponent(ip)).then(function(r){return r.json();}).then(function(d){';
        $out .= '  if(d.online){online++;var el=document.getElementById("sidebarDimmOnline");if(el)el.textContent=online;}';
        $out .= '}).catch(function(){});});})();';
    }
    $out .= '</script>';

    return $out;
}

/**
 * Render a breadcrumb trail.
 * $crumbs = [['label'=>'Dashboard','href'=>'menu.php'], ..., ['label'=>'Current Page']]
 * Last item has no href (current page).
 */
function breadcrumb_render(array $crumbs) {
    $out = '<nav class="breadcrumb" aria-label="Breadcrumb">';
    $last = count($crumbs) - 1;
    foreach ($crumbs as $i => $c) {
        if ($i > 0) {
            $out .= '<span class="breadcrumb__sep" aria-hidden="true">›</span>';
        }
        if ($i < $last) {
            $out .= '<a href="' . htmlspecialchars($c['href'], ENT_QUOTES, 'UTF-8') . '" class="breadcrumb__link">' . htmlspecialchars($c['label'], ENT_QUOTES, 'UTF-8') . '</a>';
        } else {
            $out .= '<span class="breadcrumb__current">' . htmlspecialchars($c['label'], ENT_QUOTES, 'UTF-8') . '</span>';
        }
    }
    $out .= '</nav>';
    return $out;
}

function load_ui_styles() {
    echo '<link rel="stylesheet" href="css/modern-theme.css">' . "\n";
    echo '<link rel="stylesheet" href="css/components.css">' . "\n";
    echo '<link rel="stylesheet" href="css/arcade-icons.css">' . "\n";
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">' . "\n";
    echo '<link rel="stylesheet" href="css/arcade-retro.css">' . "\n";
    // Prevent flash of wrong theme — runs before first paint
    echo '<script>(function(){var t=localStorage.getItem("wipi-theme");if(t)document.documentElement.setAttribute("data-theme",t);})();</script>' . "\n";
}

function theme_toggle_js() {
    return <<<'JS'
<script>
function toggleTheme() {
    var html = document.documentElement;
    var cur = html.getAttribute('data-theme');
    // If no explicit theme, check computed background to detect current mode
    if (!cur) {
        var bg = getComputedStyle(html).getPropertyValue('--color-background').trim();
        cur = (bg === '#FFFFFF' || bg === '#ffffff') ? 'light' : 'dark';
    }
    var next = (cur === 'dark') ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('wipi-theme', next);
    var icon = document.getElementById('themeIcon');
    var label = document.getElementById('themeLabel');
    if (icon) icon.textContent = next === 'dark' ? '☀' : '◑';
    if (label) label.textContent = next === 'dark' ? 'Light mode' : 'Dark mode';
}
// Set correct icon on load
(function(){
    var t = localStorage.getItem('wipi-theme') || 'dark';
    var icon = document.getElementById('themeIcon');
    var label = document.getElementById('themeLabel');
    if (icon) icon.textContent = t === 'dark' ? '☀' : '◑';
    if (label) label.textContent = t === 'dark' ? 'Light mode' : 'Dark mode';
})();
</script>
JS;
}
