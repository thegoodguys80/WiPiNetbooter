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
        ['key' => 'uimode',   'href' => 'ui-mode-switcher.php',        'icon' => 'uimode',    'label' => 'UI Mode'],
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

    // Theme picker (4 swatches)
    $out .= '<div class="sidebar-theme-picker" id="themePicker">';
    $out .= '<span class="sidebar-nav-label sidebar-theme-picker__label">THEME</span>';
    $out .= '<div class="sidebar-theme-swatches">';
    $out .= '<button class="theme-swatch" data-theme="arcade"    onclick="setTheme(\'arcade\')"    title="Arcade (Cyan)"    aria-label="Arcade theme"    aria-pressed="false"><span class="swatch-dot" style="background:#00d4ff;"></span></button>';
    $out .= '<button class="theme-swatch" data-theme="light"     onclick="setTheme(\'light\')"     title="Light"            aria-label="Light theme"     aria-pressed="false"><span class="swatch-dot" style="background:#1a56dc;"></span></button>';
    $out .= '<button class="theme-swatch" data-theme="sega-blue" onclick="setTheme(\'sega-blue\')" title="Sega Blue"        aria-label="Sega Blue theme" aria-pressed="false"><span class="swatch-dot" style="background:#ff6600;"></span></button>';
    $out .= '<button class="theme-swatch" data-theme="terminal"  onclick="setTheme(\'terminal\')"  title="Terminal Green"   aria-label="Terminal theme"  aria-pressed="false"><span class="swatch-dot" style="background:#00ff41;"></span></button>';
    $out .= '</div></div>';
    $out .= '</div>'; // sidebar-footer

    $out .= '</nav></div>';
    $out .= '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    $out .= '<div class="main-content">';

    // JS: theme picker + async status checks
    $out .= '<script>';
    $out .= 'function setTheme(name){';
    $out .= '  document.documentElement.setAttribute("data-theme",name);';
    $out .= '  localStorage.setItem("wipi-theme",name);';
    $out .= '  _updateSwatches(name);';
    $out .= '}';
    $out .= 'function _updateSwatches(t){';
    $out .= '  document.querySelectorAll(".theme-swatch").forEach(function(b){';
    $out .= '    var active=b.dataset.theme===t;';
    $out .= '    b.classList.toggle("active",active);';
    $out .= '    b.setAttribute("aria-pressed",active?"true":"false");';
    $out .= '  });';
    $out .= '}';
    $out .= '(function(){';
    $out .= '  var t=localStorage.getItem("wipi-theme")||"arcade";';
    $out .= '  if(t==="dark")t="arcade";'; // backward compat
    $out .= '  document.documentElement.setAttribute("data-theme",t);';
    $out .= '  document.addEventListener("DOMContentLoaded",function(){_updateSwatches(t);});';
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
    echo '<script>(function(){var t=localStorage.getItem("wipi-theme")||"arcade";if(t==="dark")t="arcade";document.documentElement.setAttribute("data-theme",t);})();</script>' . "\n";
}

function theme_toggle_js() {
    // Legacy stub — theme switching now handled by modern_sliding_sidebar_nav()
    return '';
}
