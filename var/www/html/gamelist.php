<?php
// Game list page with modern UI
include 'ui_mode.php';
$ui_mode = get_ui_mode();

// Load all games from CSV
$sample_games = [];
if (file_exists('csv/romsinfo.csv')) {
    $f = fopen('csv/romsinfo.csv', 'r');
    $headers = fgetcsv($f); // Skip header row
    while (($row = fgetcsv($f)) !== false) {
        // Only include enabled games
        if (isset($row[12]) && $row[12] === 'Yes') {
            $sample_games[] = [
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
    fclose($f);
} else {
    // Fallback sample data if CSV doesn't exist
    $sample_games = [
        ['Sega Naomi', '18wheeler.bin.gz', '18wheelr.png', '18wheelr.mp4', '18 Wheeler Deluxe', 'Sega', '2000', 'Driving', 'Yes', '18-wheeler', 'generic-driving'],
        ['Sega Naomi', 'mvsc2.bin.gz', 'mvsc2.png', 'mvsc2.mp4', 'Marvel vs Capcom 2', 'Capcom', '2000', 'Fighter', 'Yes', 'generic-4-button', 'none'],
        ['Sega Naomi', 'ikaruga.bin.gz', 'ikaruga.png', 'ikaruga.mp4', 'Ikaruga', 'Treasure', '2001', 'Shooter', 'No', 'generic-analogue', 'none'],
    ];
}

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Game Library</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';
load_ui_styles();
echo '</head><body>';

if ($ui_mode === 'modern') {
    // Modern UI
    echo '<button class="burger-menu" id="burgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu"><span></span><span></span><span></span></button>';
    echo '<div class="sidebar-nav" id="sidebarNav">';
    echo '<nav>';
    echo '<a href="menu.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">📊</span><span class="sidebar-nav-label">Dashboard</span></a>';
    echo '<a href="gamelist.php" class="sidebar-nav-item active">';
    echo '<span class="sidebar-nav-icon">🎮</span><span class="sidebar-nav-label">Games</span></a>';
    echo '<a href="dimms.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">💾</span><span class="sidebar-nav-label">NetDIMMs</span></a>';
    echo '<a href="setup.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">⚙️</span><span class="sidebar-nav-label">Setup</span></a>';
    echo '<a href="ui-mode-switcher.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎨</span><span class="sidebar-nav-label">UI Mode</span></a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    echo '<div class="main-content">';
    
    echo '<div class="container" style="padding: 20px;">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 24px;">';
    echo '<h1 class="text-3xl" style="margin: 0;">🎮 Game Library</h1>';
    echo '<input type="text" id="searchInput" class="form-input" placeholder="🔍 Search games..." style="max-width: 300px;" oninput="filterGames()">';
    echo '</div>';
    
    
    // Stats bar
    echo '<div class="grid grid-cols-4" style="margin-bottom: 32px; gap: 16px;">';
    echo '<div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">';
    echo '<div class="card-body" style="text-align: center;">';
    echo '<div style="font-size: 32px; font-weight: bold; margin-bottom: 4px;">'.count($sample_games).'</div>';
    echo '<div style="font-size: 14px; opacity: 0.9;">Total Games</div>';
    echo '</div></div>';
    
    $faves = count(array_filter($sample_games, function($g) { return $g[8] === 'Yes'; }));
    echo '<div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">';
    echo '<div class="card-body" style="text-align: center;">';
    echo '<div style="font-size: 32px; font-weight: bold; margin-bottom: 4px;">⭐ '.$faves.'</div>';
    echo '<div style="font-size: 14px; opacity: 0.9;">Favourites</div>';
    echo '</div></div>';
    
    $systems = array_unique(array_column($sample_games, 0));
    echo '<div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none;">';
    echo '<div class="card-body" style="text-align: center;">';
    echo '<div style="font-size: 32px; font-weight: bold; margin-bottom: 4px;">'.count($systems).'</div>';
    echo '<div style="font-size: 14px; opacity: 0.9;">Systems</div>';
    echo '</div></div>';
    
    $genres = array_unique(array_column($sample_games, 7));
    echo '<div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border: none;">';
    echo '<div class="card-body" style="text-align: center;">';
    echo '<div style="font-size: 32px; font-weight: bold; margin-bottom: 4px;">'.count($genres).'</div>';
    echo '<div style="font-size: 14px; opacity: 0.9;">Genres</div>';
    echo '</div></div>';
    echo '</div>';
    
    // Filter dropdowns
    echo '<div class="flex" style="gap: 12px; flex-wrap: wrap; margin-bottom: 24px; align-items: center;">';
    echo '<label style="color: #aaa; font-weight: 600;">Filter by:</label>';
    
    // System dropdown
    echo '<select id="systemFilter" onchange="filterBySystem(this.value)" class="form-select" style="min-width: 180px; padding: 8px 12px; border: 1px solid #444; border-radius: 6px; background: #2a2a2a; color: #fff;">';
    echo '<option value="all">All Systems</option>';
    foreach ($systems as $system) {
        echo '<option value="'.strtolower($system).'">'.$system.'</option>';
    }
    echo '</select>';
    
    // Genre dropdown
    echo '<select id="genreFilter" onchange="filterByGenre(this.value)" class="form-select" style="min-width: 180px; padding: 8px 12px; border: 1px solid #444; border-radius: 6px; background: #2a2a2a; color: #fff;">';
    echo '<option value="all">All Genres</option>';
    foreach ($genres as $genre) {
        echo '<option value="'.strtolower($genre).'">'.$genre.'</option>';
    }
    echo '</select>';
    
    // Reset button
    echo '<button class="btn btn-secondary" onclick="resetFilters()" style="padding: 8px 16px;">Reset Filters</button>';
    echo '</div>';
    
    // Game grid - optimized for touchscreens
    echo '<div id="gameGrid" class="game-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">';
    echo '<style>';
    echo '@media (max-width: 1024px) { ';
    // Game grid and cards - very compact with snap scrolling
    echo '  #gameGrid { grid-template-columns: 1fr !important; gap: 0px !important; max-width: 100% !important; scroll-snap-type: y mandatory !important; overflow-y: auto !important; } ';
    echo '  .game-card { max-width: 100% !important; scroll-snap-align: start !important; margin-bottom: 6px !important; } ';
    echo '  .game-card-image-container { margin-bottom: 0 !important; padding: 0 !important; } ';
    echo '  .game-card-img-box { height: 120px !important; font-size: 42px !important; margin: 0 !important; } ';
    echo '  .game-card-content { padding: 4px 10px 6px 10px !important; } ';
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
    echo '  .grid.grid-cols-4 { display: none !important; } ';
    echo '  .flex:has(label) { display: none !important; } '; // Hide filters completely
    echo '  h1.text-3xl { font-size: 18px !important; margin: 0 !important; } ';
    echo '  .flex:has(h1) { margin-bottom: 8px !important; padding: 0 !important; } ';
    echo '  #searchInput { max-width: 180px !important; font-size: 13px !important; padding: 6px 10px !important; } ';
    echo '  .container { padding: 8px !important; } ';
    echo '}';
    echo '</style>';
    
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
        
        echo '<div class="game-card" data-name="'.strtolower($title).'" data-system="'.strtolower($system).'" data-genre="'.strtolower($genre).'">';
        echo '<div class="game-card-image-container">';
        
        // Always show placeholder with image attempt on top
        $image_path = 'images/' . $image;
        $initial = substr($title, 0, 1);
        $colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#fa709a', '#fee140'];
        $color = $colors[ord($initial) % count($colors)];
        $fallback_color = adjustBrightness($color, -20);
        
        // Container with placeholder background
        echo '<div class="game-card-img-box" style="position: relative; width: 100%; height: 280px; background: linear-gradient(135deg, '.$color.' 0%, '.$fallback_color.' 100%); display: flex; align-items: center; justify-content: center; font-size: 72px; font-weight: bold; color: white; border-radius: 8px; overflow: hidden;">';
        
        // Show letter
        echo '<span style="position: absolute; z-index: 1;">'.$initial.'</span>';
        
        // Try to overlay real image if it exists
        echo '<img src="'.$image_path.'" alt="'.$title.'" ';
        echo 'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; object-position: center; z-index: 2; background: transparent;" ';
        echo 'onload="this.previousElementSibling.style.display=\'none\';" ';
        echo 'onerror="this.style.display=\'none\';">';
        
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
        echo '<button class="game-card-info" onclick="showGameInfo('.htmlspecialchars($info_data, ENT_QUOTES).'); event.preventDefault();" title="Game Information" style="position: absolute; top: 12px; right: 12px; width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,0.7); border: 2px solid #4a9eff; color: #4a9eff; font-size: 20px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background=\'#4a9eff\'; this.style.color=\'#fff\';" onmouseout="this.style.background=\'rgba(0,0,0,0.7)\'; this.style.color=\'#4a9eff\';">i</button>';
        
        echo '</div>';
        
        echo '<div class="game-card-content">';
        echo '<h3 class="game-card-title">'.$title.'</h3>';
        echo '<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;">';
        echo '<span class="game-card-system-badge '.strtolower(str_replace(' ', '-', $system)).'">'.$system.'</span>';
        echo '<span style="background: #2a2a2a; color: #aaa; padding: 4px 8px; border-radius: 4px; font-size: 11px;">'.$genre.'</span>';
        echo '</div>';
        echo '<div style="margin-top: 8px; font-size: 12px; color: #888;">';
        echo $manufacturer.' • '.$year;
        echo '</div>';
        
        // Launch button at bottom of card
        echo '<a href="loadcheck.php?rom='.$filename.'&name='.urlencode($title).'&system='.urlencode($system).'&mapping='.$mapping.'&ffb='.$ffb.'" class="btn btn-primary" style="display: block; width: 100%; margin-top: 12px; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 15px; font-weight: 600;">Launch</a>';
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>'; // Close game grid
    echo '</div>'; // Close container
    echo '</div>'; // Close main-content
    
    // Game info modal
    echo '<div id="gameInfoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;" onclick="closeGameInfo()">';
    echo '<div style="background: #1a1a1a; border-radius: 12px; padding: 32px; max-width: 900px; width: 90%; border: 2px solid #4a9eff; box-shadow: 0 8px 32px rgba(0,0,0,0.5);" onclick="event.stopPropagation();">';
    
    // Game image at top center (no text title)
    echo '<div style="text-align: center; margin-bottom: 24px;">';
    echo '<img id="modalImage" src="" alt="Game Title" style="max-width: 300px; max-height: 200px; border-radius: 8px; background: transparent;">';
    echo '</div>';
    
    // Two column layout: Game info (left) and Screenshot (right)
    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">';
    
    // Left: Game information
    echo '<div style="display: grid; gap: 16px;">';
    echo '<div><strong style="color: #aaa;">System:</strong> <span id="modalSystem" style="color: #fff;"></span></div>';
    echo '<div><strong style="color: #aaa;">Genre:</strong> <span id="modalGenre" style="color: #fff;"></span></div>';
    echo '<div><strong style="color: #aaa;">Manufacturer:</strong> <span id="modalManufacturer" style="color: #fff;"></span></div>';
    echo '<div><strong style="color: #aaa;">Year:</strong> <span id="modalYear" style="color: #fff;"></span></div>';
    echo '<div><strong style="color: #aaa;">ROM File:</strong> <span id="modalFilename" style="color: #888; font-size: 12px; word-break: break-all;"></span></div>';
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
    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">';
    echo '<button id="modalLaunchBtn" onclick="launchGame()" style="padding: 16px 12px; background: #28a745; color: #fff; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; min-height: 56px;" onmouseover="this.style.background=\'#218838\'" onmouseout="this.style.background=\'#28a745\'">Launch</button>';
    echo '<button onclick="closeGameInfo()" style="padding: 16px 12px; background: #6c757d; color: #fff; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; min-height: 56px;" onmouseover="this.style.background=\'#5a6268\'" onmouseout="this.style.background=\'#6c757d\'">Close</button>';
    echo '</div>';
    echo '</div></div>';
    
    // Scripts
    echo '<script>';
    echo 'let activeSystemFilter="all";let activeGenreFilter="all";let searchQuery="";let currentGameData={};';
    echo 'function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}';
    echo 'function applyFilters(){document.querySelectorAll(".game-card").forEach(c=>{const name=c.getAttribute("data-name");const sys=c.getAttribute("data-system");const genre=c.getAttribute("data-genre");const matchSearch=searchQuery===""||name.includes(searchQuery);const matchSystem=activeSystemFilter==="all"||sys.includes(activeSystemFilter);const matchGenre=activeGenreFilter==="all"||genre.includes(activeGenreFilter);c.style.display=(matchSearch&&matchSystem&&matchGenre)?"block":"none";});}';
    echo 'function filterGames(){searchQuery=document.getElementById("searchInput").value.toLowerCase();applyFilters();}';
    echo 'function filterBySystem(s){activeSystemFilter=s;applyFilters();}';
    echo 'function filterByGenre(g){activeGenreFilter=g;applyFilters();}';
    echo 'function resetFilters(){activeSystemFilter="all";activeGenreFilter="all";searchQuery="";document.getElementById("systemFilter").value="all";document.getElementById("genreFilter").value="all";document.getElementById("searchInput").value="";applyFilters();}';
    echo 'function showGameInfo(data){currentGameData=data;document.getElementById("modalSystem").textContent=data.system;document.getElementById("modalGenre").textContent=data.genre;document.getElementById("modalManufacturer").textContent=data.manufacturer;document.getElementById("modalYear").textContent=data.year;document.getElementById("modalFilename").textContent=data.filename;const img=document.getElementById("modalImage");const imgContainer=img.parentElement;if(data.image){img.src=data.image;img.onerror=function(){imgContainer.style.display="none";};img.onload=function(){imgContainer.style.display="block";};imgContainer.style.display="block";}else{imgContainer.style.display="none";}const screenshot=document.getElementById("modalScreenshot");const screenshotContainer=document.getElementById("modalScreenshotContainer");if(data.screenshot){screenshot.src=data.screenshot;screenshot.onerror=function(){screenshotContainer.style.display="none";};screenshot.onload=function(){screenshotContainer.style.display="block";};screenshotContainer.style.display="block";}else{screenshotContainer.style.display="none";}const video=document.getElementById("modalVideo");const videoSource=document.getElementById("modalVideoSource");const videoContainer=document.getElementById("modalVideoContainer");if(data.video){videoSource.src=data.video;video.load();videoContainer.style.display="block";}else{videoContainer.style.display="none";}document.getElementById("gameInfoModal").style.display="flex";}';
    echo 'function launchGame(){window.location.href="loadcheck.php?rom="+encodeURIComponent(currentGameData.filename)+"&name="+encodeURIComponent(currentGameData.title)+"&system="+encodeURIComponent(currentGameData.system)+"&mapping="+encodeURIComponent(currentGameData.mapping)+"&ffb="+encodeURIComponent(currentGameData.ffb);}';
    echo 'function closeGameInfo(){const video=document.getElementById("modalVideo");video.pause();video.currentTime=0;document.getElementById("gameInfoModal").style.display="none";}';
    echo '</script>';
    
} else {
    // Classic UI - redirect to old gamelist
    header('Location: gamelist.php.backup');
    exit();
}

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
