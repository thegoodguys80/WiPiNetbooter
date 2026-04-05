<?php
// Game list page with modern UI
include 'ui_mode.php';
include_once __DIR__ . '/includes/gamelist_visibility.php';

// Load all games from CSV (before hidden filter)
$all_games = [];
$rom_folder = '/boot/roms/';
if (file_exists('csv/romsinfo.csv')) {
    $f = fopen('csv/romsinfo.csv', 'r');
    $headers = fgetcsv($f); // Skip header row
    while (($row = fgetcsv($f)) !== false) {
        // Only include enabled games AND if ROM file exists
        if (isset($row[12]) && $row[12] === 'Yes' && isset($row[1])) {
            $rom_path = $rom_folder . $row[1];
            // Check if ROM file exists before adding to list
            if (file_exists($rom_path)) {
                $all_games[] = [
                    $row[0],  // 0: system
                    $row[1],  // 1: romname
                    $row[2],  // 2: image
                    $row[3],  // 3: video
                    $row[4],  // 4: description (title)
                    $row[6],  // 5: manufacturer
                    $row[7],  // 6: year
                    $row[8],  // 7: genre
                    $row[13], // 8: favourite
                    $row[14], // 9: openjvs (mapping)
                    $row[15], // 10: openffb (ffb)
                ];
            }
        }
    }
    fclose($f);
} else {
    // Fallback sample data if CSV doesn't exist
    $all_games = [
        ['Sega Naomi', '18wheeler.bin.gz', '18wheelr.png', '18wheelr.mp4', '18 Wheeler Deluxe', 'Sega', '2000', 'Driving', 'Yes', '18-wheeler', 'generic-driving'],
        ['Sega Naomi', 'mvsc2.bin.gz', 'mvsc2.png', 'mvsc2.mp4', 'Marvel vs Capcom 2', 'Capcom', '2000', 'Fighter', 'Yes', 'generic-4-button', 'none'],
        ['Sega Naomi', 'ikaruga.bin.gz', 'ikaruga.png', 'ikaruga.mp4', 'Ikaruga', 'Treasure', '2001', 'Shooter', 'No', 'generic-analogue', 'none'],
    ];
}

$hidden_roms = gamelist_hidden_load();
$hidden_set = array_flip($hidden_roms);
$show_hidden = isset($_GET['show_hidden']) && $_GET['show_hidden'] === '1';

$sample_games = array_values(array_filter($all_games, function ($g) use ($hidden_set, $show_hidden) {
    if ($show_hidden) {
        return true;
    }
    $rom = $g[1];
    return empty($hidden_set[$rom]);
}));

$hidden_count = 0;
foreach ($all_games as $g) {
    if (!empty($hidden_set[$g[1]])) {
        $hidden_count++;
    }
}

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Game Library</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';
load_ui_styles();
echo '</head><body>';

echo modern_sliding_sidebar_nav('games');
    
    echo '<div class="container p-6">';
    echo '<div class="game-library-header game-library-header--toolbar">';
    echo '<h1 class="text-3xl">'.arcade_icon('games').' Game Library</h1>';
    echo '<div class="game-library-header__controls">';
    echo '<input type="search" inputmode="search" id="searchInput" class="form-input search-input--library" placeholder="Search games…" oninput="filterGames()" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">';
    $chk = $show_hidden ? ' checked' : '';
    echo '<label class="game-library-show-hidden"><input type="checkbox" id="showHiddenToggle"' . $chk . ' onchange="window.location.href=this.checked?\'gamelist.php?show_hidden=1\':\'gamelist.php\'"> Show hidden</label>';
    echo '</div>';
    echo '</div>';
    
    
    // Stats bar (5 tiles — auto-fit)
    echo '<div class="game-library-stats grid gap-4 mb-8" style="grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));">';
    echo '<div class="stat-card">';
    echo '<div class="stat-card__value">'.count($sample_games).'</div>';
    echo '<div class="stat-card__label">'.($show_hidden ? 'Shown (incl. hidden)' : 'Games in list').'</div>';
    echo '</div>';
    
    echo '<div class="stat-card">';
    echo '<div class="stat-card__value">'.$hidden_count.'</div>';
    echo '<div class="stat-card__label">Hidden from list</div>';
    echo '</div>';

    $faves = count(array_filter($sample_games, function($g) { return $g[8] === 'Yes'; }));
    echo '<div class="stat-card">';
    echo '<div class="stat-card__value">'.arcade_icon('favorites').' '.$faves.'</div>';
    echo '<div class="stat-card__label">Favourites</div>';
    echo '</div>';
    
    $systems = array_unique(array_column($sample_games, 0));
    echo '<div class="stat-card">';
    echo '<div class="stat-card__value">'.count($systems).'</div>';
    echo '<div class="stat-card__label">Systems</div>';
    echo '</div>';
    
    $genres = array_unique(array_column($sample_games, 7));
    echo '<div class="stat-card">';
    echo '<div class="stat-card__value">'.count($genres).'</div>';
    echo '<div class="stat-card__label">Genres</div>';
    echo '</div>';
    echo '</div>';
    
    // System filter tabs — always visible including mobile
    echo '<div class="sys-tabs" id="sysTabs">';
    echo '<button class="sys-tab active" data-tab="all"       onclick="filterByTab(\'all\')">All</button>';
    echo '<button class="sys-tab"        data-tab="naomi"     onclick="filterByTab(\'naomi\')">Naomi</button>';
    echo '<button class="sys-tab"        data-tab="naomi2"    onclick="filterByTab(\'naomi2\')">Naomi 2</button>';
    echo '<button class="sys-tab"        data-tab="atomiswave" onclick="filterByTab(\'atomiswave\')">Atomiswave</button>';
    echo '</div>';
    echo '<style>';
    echo '.sys-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;}';
    echo '.sys-tab{padding:8px 20px;border-radius:20px;border:2px solid var(--color-border,#333);background:transparent;color:var(--color-text-secondary,#aaa);cursor:pointer;font-size:14px;font-weight:600;transition:all .2s;}';
    echo '.sys-tab.active,.sys-tab:hover{background:var(--color-primary,#2563eb);border-color:var(--color-primary,#2563eb);color:#fff;}';
    echo '</style>';

    // Filter dropdowns
    echo '<div class="filter-toolbar">';
    echo '<span class="filter-toolbar__label">Filter by</span>';
    
    // System dropdown
    echo '<select id="systemFilter" onchange="filterBySystem(this.value)" class="form-select form-select--compact">';
    echo '<option value="all">All Systems</option>';
    foreach ($systems as $system) {
        echo '<option value="'.strtolower($system).'">'.$system.'</option>';
    }
    echo '</select>';
    
    // Genre dropdown
    echo '<select id="genreFilter" onchange="filterByGenre(this.value)" class="form-select form-select--compact">';
    echo '<option value="all">All Genres</option>';
    foreach ($genres as $genre) {
        echo '<option value="'.strtolower($genre).'">'.$genre.'</option>';
    }
    echo '</select>';
    
    // Reset button
    echo '<button type="button" class="btn btn-secondary btn-sm" onclick="resetFilters()">Reset filters</button>';
    echo '</div>';
    
    // Game grid - optimized for touchscreens
    echo '<div id="gameGrid" class="game-grid game-grid-fadein" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">';
    echo '<style>';
    // Default desktop styles - eliminate white space
    echo '.game-card-img-box { height: 280px; }';
    echo '.game-card { display: flex; flex-direction: column; gap: 0; padding: 0; }';
    echo '.game-card > * { margin: 0; }';
    echo '.game-card-image-container { margin: 0; padding: 0; display: block; line-height: 0; aspect-ratio: unset; height: auto; }';
    echo '.game-card-image-container > * { margin: 0; padding: 0; }';
    echo '.game-card-content { padding: 12px; margin: 0; }';
    // Ensure burger menu is always visible
    echo '.game-library-header--toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:var(--space-4);margin-bottom:var(--space-6);}';
    echo '.game-library-header__controls{display:flex;flex-wrap:wrap;align-items:center;gap:var(--space-3);}';
    echo '.game-library-show-hidden{display:inline-flex;align-items:center;gap:8px;font-size:var(--font-size-sm);color:var(--color-text-secondary);cursor:pointer;white-space:nowrap;}';
    echo '.game-library-show-hidden input{accent-color:var(--arcade-cyan,#00d4ff);width:18px;height:18px;}';
    echo '.game-card--hidden-from-list{box-shadow:0 0 0 2px rgba(245,158,11,0.55);}';
    echo '.burger-menu { display: flex !important; }';
    echo '.sidebar-nav-item { display: flex !important; }';
    echo '@media (max-width: 1024px) { ';
    // Game grid and cards - very compact with snap scrolling
    echo '  #gameGrid { grid-template-columns: 1fr !important; gap: 0px !important; max-width: 100% !important; scroll-snap-type: y mandatory !important; overflow-y: auto !important; } ';
    echo '  .game-card { max-width: 100% !important; scroll-snap-align: start !important; margin-bottom: 6px !important; display: flex !important; flex-direction: column !important; gap: 0 !important; padding: 0 !important; } ';
    echo '  .game-card > * { margin: 0 !important; } ';
    echo '  .game-card-image-container { margin: 0 !important; padding: 0 !important; display: block !important; line-height: 0 !important; aspect-ratio: unset !important; height: auto !important; } ';
    echo '  .game-card-image-container > * { margin: 0 !important; padding: 0 !important; } ';
    echo '  .game-card-img-box { height: 120px !important; font-size: 42px !important; margin: 0 !important; padding: 0 !important; display: flex !important; } ';
    echo '  .game-card-content { padding: 4px 10px 6px 10px !important; margin: 0 !important; margin-top: 0 !important; } ';
    echo '  .game-card-title { font-size: 14px !important; margin: 0 0 3px 0 !important; line-height: 1.1 !important; } ';
    echo '  .game-card-system-badge { font-size: 11px !important; padding: 2px 6px !important; } ';
    echo '  .game-card-content > div:nth-child(2) { margin-top: 0 !important; gap: 6px !important; } ';
    echo '  .game-card-content > div:nth-child(2) > span { margin: 0 !important; } ';
    echo '  .game-card-content > div:nth-child(3) { margin-top: 2px !important; font-size: 10px !important; } ';
    echo '  .game-card-content a.btn { margin-top: 4px !important; padding: 9px 10px !important; font-size: 14px !important; } ';
    // Modal optimizations
    echo '  #gameInfoModal > div { padding: 16px !important; max-width: 95% !important; max-height: 90vh !important; overflow-y: auto !important; } ';
    echo '  #modalImage { max-width: 100% !important; max-height: 120px !important; } ';
    echo '  #gameInfoModal > div > div:first-child { margin-bottom: 16px !important; } ';
    echo '  #gameInfoModal > div > div:nth-child(2) { grid-template-columns: 1fr !important; gap: 12px !important; } ';
    echo '  #modalScreenshot { max-height: 180px !important; } ';
    echo '  #modalVideo { max-height: 220px !important; } ';
    echo '  #modalVideoContainer { margin-bottom: 16px !important; } ';
    echo '  #modalVideoContainer > div:first-child { font-size: 13px !important; margin-bottom: 8px !important; } ';
    echo '  #modalLaunchBtn, #gameInfoModal button { min-height: 52px !important; font-size: 17px !important; } ';
    // Hide/minimize less critical elements on small screens
    echo '  .game-library-stats { display: none !important; } ';
    echo '  .filter-toolbar { display: none !important; } '; // Hide filters on small touch layouts
    echo '  h1.text-3xl { font-size: 18px !important; margin: 0 !important; } ';
    echo '  .game-library-header { margin-bottom: 8px !important; } ';
    echo '  .game-library-show-hidden { font-size: 12px !important; } ';
    echo '  #searchInput { max-width: 180px !important; font-size: 13px !important; padding: 6px 10px !important; } ';
    echo '  .container { padding: 8px !important; } ';
    echo '}';
    echo '</style>';
    
    // Check if no games available
    if (empty($sample_games)) {
        echo '<div class="empty-state">';
        echo '<div>';
        echo arcade_icon('cabinet', 'empty-state__icon arcade-icon--lg');
        if (!empty($all_games) && !$show_hidden && $hidden_count > 0) {
            echo '<h2 class="empty-state__title">All games hidden from list</h2>';
            echo '<p class="text-base text-secondary mb-2">Turn on <strong>Show hidden</strong> above to manage entries, or edit <code class="empty-state__path">csv/gamelist_hidden.txt</code>.</p>';
        } else {
            echo '<h2 class="empty-state__title">No games available</h2>';
            echo '<p class="text-base text-secondary mb-2">Add ROM files to <code class="empty-state__path">/boot/roms/</code></p>';
            echo '<p class="text-sm text-secondary m-0">Only games with ROM files present are listed.</p>';
        }
        echo '</div>';
        echo '</div>';
    }
    
    foreach ($sample_games as $game) {
        $system = $game[0];
        $filename = $game[1];
        $image = $game[2];
        $video = $game[3];
        $title = $game[4];
        $manufacturer = $game[5];
        $year = $game[6];
        $genre = $game[7];
        $fave = $game[8];
        $mapping = $game[9];
        $ffb = $game[10];
        $is_hidden = !empty($hidden_set[$filename]);
        $card_class = 'game-card';
        if ($show_hidden && $is_hidden) {
            $card_class .= ' game-card--hidden-from-list';
        }
        
        echo '<div class="'.htmlspecialchars($card_class, ENT_QUOTES, 'UTF-8').'" data-name="'.strtolower($title).'" data-system="'.strtolower($system).'" data-genre="'.strtolower($genre).'">';
        echo '<div class="game-card-image-container">';
        
        // Always show placeholder with image attempt on top
        $image_path = 'images/' . $image;
        $initial = substr($title, 0, 1);
        $color = '#2563EB';
        $fallback_color = adjustBrightness($color, -18);
        
        // Container with placeholder background
        echo '<div class="game-card-img-box skeleton" id="imgbox_'.md5($filename).'" style="position: relative; width: 100%; background: linear-gradient(135deg, '.$color.' 0%, '.$fallback_color.' 100%); display: flex; align-items: center; justify-content: center; font-size: 72px; font-weight: bold; color: white; border-radius: 8px; overflow: hidden;">';
        
        // Show letter
        echo '<span style="position: absolute; z-index: 1;">'.$initial.'</span>';
        
        // Try to overlay real image if it exists
        $boxid = 'imgbox_'.md5($filename);
        echo '<img src="'.$image_path.'" alt="'.$title.'" ';
        echo 'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; object-position: center; z-index: 2; background: transparent;" ';
        echo 'onload="this.previousElementSibling.style.display=\'none\';var b=document.getElementById(\''.addslashes($boxid).'\');if(b)b.classList.remove(\'skeleton\');" ';
        echo 'onerror="this.style.display=\'none\';var b=document.getElementById(\''.addslashes($boxid).'\');if(b)b.classList.remove(\'skeleton\');">';
        
        echo '</div>';
        
        if ($fave == 'Yes') {
            echo '<button class="game-card-favorite active" title="Remove from favorites">⭐</button>';
        }
        
        // Info button
        // Convert video filename to screenshot (replace .mp4 with _screenshot.png)
        $screenshot_path = '';
        $video_path = '';
        if (!empty($video)) {
            $screenshot_path = 'images/' . str_replace('.mp4', '_screenshot.png', $video);
            $video_path = 'videos/' . $video;
        }
        $info_data = json_encode([
            'title' => $title,
            'system' => $system,
            'genre' => $genre,
            'manufacturer' => $manufacturer,
            'year' => $year,
            'filename' => $filename,
            'image' => $image_path,
            'screenshot' => $screenshot_path,
            'video' => $video_path,
            'mapping' => $mapping,
            'ffb' => $ffb
        ]);
        echo '<button type="button" class="game-card-info-btn" onclick="showGameInfo('.htmlspecialchars($info_data, ENT_QUOTES).'); event.preventDefault();" title="Game information">i</button>';
        
        echo '</div>';
        
        echo '<div class="game-card-content">';
        echo '<h3 class="game-card-title">'.$title.'</h3>';
        echo '<div class="flex gap-2 flex-wrap mt-2">';
        echo '<span class="game-card-system-badge '.strtolower(str_replace(' ', '-', $system)).'">'.$system.'</span>';
        echo '<span class="game-card-genre-pill">'.$genre.'</span>';
        echo '</div>';
        echo '<div class="game-card-meta-line">';
        echo $manufacturer.' • '.$year;
        echo '</div>';
        
        // Launch button at bottom of card
        $launch_url = 'loadcheck.php?rom='.urlencode($filename).'&name='.urlencode($title).'&system='.urlencode($system).'&mapping='.urlencode($mapping).'&ffb='.urlencode($ffb);
        echo '<button type="button" onclick="showLaunchOverlay('.json_encode($title).', '.json_encode($image_path).', '.json_encode($launch_url).')" class="btn btn-primary btn-block mt-4 font-semibold">Launch</button>';

        $toggle_params = ['rom' => $filename, 'action' => $is_hidden ? 'unhide' : 'hide'];
        if ($show_hidden) {
            $toggle_params['show_hidden'] = '1';
        }
        $toggle_href = 'gamelist_toggle_visibility.php?' . http_build_query($toggle_params);
        $toggle_label = $is_hidden ? 'Show in main list' : 'Hide from main list';
        echo '<a href="'.htmlspecialchars($toggle_href, ENT_QUOTES, 'UTF-8').'" class="btn btn-secondary btn-sm btn-block mt-2">'.$toggle_label.'</a>';
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>'; // Close game grid
    echo '</div>'; // Close container
    echo '</div>'; // Close main-content

    // Back-to-top button
    echo '<button id="backToTop" onclick="window.scrollTo({top:0,behavior:\'smooth\'})" aria-label="Back to top" title="Back to top">&#8679;</button>';
    echo '<script>window.addEventListener("scroll",function(){var b=document.getElementById("backToTop");if(b)b.style.display=window.scrollY>400?"flex":"none";});</script>';

    // Launch overlay
    echo '<div id="launchOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.93);z-index:9999;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:24px;">';
    echo '<img id="launchOverlayImg" src="" alt="" style="width:140px;height:140px;object-fit:contain;border-radius:12px;background:#111;margin-bottom:24px;" onerror="this.style.display=\'none\'">';
    echo '<h2 id="launchOverlayTitle" style="color:#fff;font-size:26px;font-weight:700;margin:0 0 10px;"></h2>';
    echo '<p style="color:#aaa;font-size:16px;margin:0 0 36px;">Sending to NetDIMM\u2026</p>';
    echo '<div style="width:48px;height:48px;border:4px solid #333;border-top-color:var(--color-primary,#2563eb);border-radius:50%;animation:spin 0.8s linear infinite;"></div>';
    echo '<style>@keyframes spin{to{transform:rotate(360deg);}}</style>';
    echo '</div>';

    // Game info modal
    echo '<div id="gameInfoModal" class="game-info-modal" onclick="closeGameInfo()">';
    echo '<div class="game-info-modal__panel" onclick="event.stopPropagation();">';
    
    // Game image at top center (no text title)
    echo '<div style="text-align: center; margin-bottom: 24px;">';
    echo '<img id="modalImage" src="" alt="Game Title" style="max-width: 300px; max-height: 200px; border-radius: 8px; background: transparent;">';
    echo '</div>';
    
    // Two column layout: Game info (left) and Screenshot (right)
    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">';
    
    // Left: Game information
    echo '<div style="display: grid; gap: 16px;">';
    echo '<div><strong class="text-secondary">System:</strong> <span id="modalSystem" class="text-primary"></span></div>';
    echo '<div><strong class="text-secondary">Genre:</strong> <span id="modalGenre" class="text-primary"></span></div>';
    echo '<div><strong class="text-secondary">Manufacturer:</strong> <span id="modalManufacturer" class="text-primary"></span></div>';
    echo '<div><strong class="text-secondary">Year:</strong> <span id="modalYear" class="text-primary"></span></div>';
    echo '<div><strong class="text-secondary">ROM file:</strong> <span id="modalFilename" class="text-xs text-tertiary" style="word-break: break-all;"></span></div>';
    echo '</div>';
    
    // Right: Gameplay screenshot
    echo '<div id="modalScreenshotContainer" style="text-align: center;">';
    echo '<img id="modalScreenshot" src="" alt="Gameplay Screenshot" style="max-width: 100%; max-height: 300px; border-radius: 8px; background: #0a0a0a; padding: 8px;">';
    echo '</div>';
    
    echo '</div>';
    
    // Video player section
    echo '<div id="modalVideoContainer" style="margin-bottom: 24px; text-align: center;">';
    echo '<div style="font-size: 14px; color: #aaa; margin-bottom: 12px; font-weight: 600;">Gameplay Video</div>';
    echo '<video id="modalVideo" controls style="max-width: 100%; max-height: 400px; border-radius: 8px; background: #000;">';
    echo '<source id="modalVideoSource" src="" type="video/mp4">';
    echo 'Your browser does not support the video tag.';
    echo '</video>';
    echo '</div>';
    
    // Action buttons - larger for touchscreens
    echo '<div class="grid grid-cols-2 gap-4">';
    echo '<button type="button" id="modalLaunchBtn" onclick="launchGame()" class="btn btn-success btn-lg">Launch</button>';
    echo '<button type="button" onclick="closeGameInfo()" class="btn btn-secondary btn-lg">Close</button>';
    echo '</div>';
    echo '</div></div>';
    
    // Scripts
    echo '<script>';
    echo 'let activeTab="all";let activeGenreFilter="all";let searchQuery="";let currentGameData={};';

    echo 'function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}';

    // Tab filter
    echo 'function filterByTab(tab){';
    echo '  activeTab=tab;';
    echo '  document.querySelectorAll(".sys-tab").forEach(t=>t.classList.remove("active"));';
    echo '  const btn=document.querySelector(".sys-tab[data-tab=\""+tab+"\"]");if(btn)btn.classList.add("active");';
    echo '  applyFilters();';
    echo '}';

    // Master filter
    echo 'function applyFilters(){';
    echo '  document.querySelectorAll(".game-card").forEach(c=>{';
    echo '    const name=c.getAttribute("data-name");';
    echo '    const sys=c.getAttribute("data-system");';
    echo '    const genre=c.getAttribute("data-genre");';
    echo '    const matchSearch=searchQuery===""||name.includes(searchQuery);';
    echo '    const matchGenre=activeGenreFilter==="all"||genre.includes(activeGenreFilter);';
    echo '    let matchTab=true;';
    echo '    if(activeTab==="naomi")matchTab=sys==="sega naomi";';
    echo '    else if(activeTab==="naomi2")matchTab=sys==="sega naomi2";';
    echo '    else if(activeTab==="atomiswave")matchTab=sys==="sammy atomiswave";';
    echo '    c.style.display=(matchSearch&&matchTab&&matchGenre)?"block":"none";';
    echo '  });';
    echo '}';

    echo 'function filterGames(){searchQuery=document.getElementById("searchInput").value.toLowerCase();applyFilters();}';
    echo 'function filterBySystem(s){applyFilters();}';
    echo 'function filterByGenre(g){activeGenreFilter=g;applyFilters();}';
    echo 'function resetFilters(){activeTab="all";activeGenreFilter="all";searchQuery="";';
    echo '  document.getElementById("systemFilter").value="all";';
    echo '  document.getElementById("genreFilter").value="all";';
    echo '  document.getElementById("searchInput").value="";';
    echo '  document.querySelectorAll(".sys-tab").forEach(t=>t.classList.remove("active"));';
    echo '  const allTab=document.querySelector(".sys-tab[data-tab=\"all\"]");if(allTab)allTab.classList.add("active");';
    echo '  applyFilters();}';

    // Launch overlay
    echo 'function showLaunchOverlay(title,imgSrc,url){';
    echo '  document.getElementById("launchOverlayTitle").textContent=title;';
    echo '  const img=document.getElementById("launchOverlayImg");';
    echo '  img.style.display=imgSrc?"":"none";img.src=imgSrc||"";';
    echo '  const ov=document.getElementById("launchOverlay");ov.style.display="flex";';
    echo '  setTimeout(()=>{window.location.href=url;},600);';
    echo '}';

    // Modal game info
    echo 'function showGameInfo(data){currentGameData=data;document.getElementById("modalSystem").textContent=data.system;document.getElementById("modalGenre").textContent=data.genre;document.getElementById("modalManufacturer").textContent=data.manufacturer;document.getElementById("modalYear").textContent=data.year;document.getElementById("modalFilename").textContent=data.filename;const img=document.getElementById("modalImage");const imgContainer=img.parentElement;if(data.image){img.src=data.image;img.onerror=function(){imgContainer.style.display="none";};img.onload=function(){imgContainer.style.display="block";};imgContainer.style.display="block";}else{imgContainer.style.display="none";}const screenshot=document.getElementById("modalScreenshot");const screenshotContainer=document.getElementById("modalScreenshotContainer");if(data.screenshot){screenshot.src=data.screenshot;screenshot.onerror=function(){screenshotContainer.style.display="none";};screenshot.onload=function(){screenshotContainer.style.display="block";};screenshotContainer.style.display="block";}else{screenshotContainer.style.display="none";}const video=document.getElementById("modalVideo");const videoSource=document.getElementById("modalVideoSource");const videoContainer=document.getElementById("modalVideoContainer");if(data.video){videoSource.src=data.video;video.load();videoContainer.style.display="block";}else{videoContainer.style.display="none";}document.getElementById("gameInfoModal").style.display="flex";}';

    // Launch from modal
    echo 'function launchGame(){';
    echo '  closeGameInfo();';
    echo '  const url="loadcheck.php?rom="+encodeURIComponent(currentGameData.filename)+"&name="+encodeURIComponent(currentGameData.title)+"&system="+encodeURIComponent(currentGameData.system)+"&mapping="+encodeURIComponent(currentGameData.mapping)+"&ffb="+encodeURIComponent(currentGameData.ffb);';
    echo '  showLaunchOverlay(currentGameData.title,currentGameData.image,url);';
    echo '}';

    echo 'function closeGameInfo(){const video=document.getElementById("modalVideo");video.pause();video.currentTime=0;document.getElementById("gameInfoModal").style.display="none";}';
    echo '</script>';

echo '</body></html>';

// Helper function to adjust color brightness
function adjustBrightness($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}
?>
