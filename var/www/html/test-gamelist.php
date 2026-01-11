<?php
// Test page to demonstrate game list with actual ROM data from CSV
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
                $row[0],  // system
                $row[1],  // romname
                $row[2],  // image
                $row[3],  // video
                $row[4],  // description
                $row[6],  // manufacturer
                $row[7],  // year
                $row[8],  // genre
                $row[13], // favourite
            ];
        }
    }
    fclose($f);
} else {
    // Fallback sample data if CSV doesn't exist
    $sample_games = [
        ['Sega Naomi', '18wheeler.bin.gz', '18wheelr.png', '18wheelr.mp4', '18 Wheeler Deluxe', 'Sega', '2000', 'Driving', 'Yes'],
        ['Sega Naomi', 'mvsc2.bin.gz', 'mvsc2.png', 'mvsc2.mp4', 'Marvel vs Capcom 2', 'Capcom', '2000', 'Fighter', 'Yes'],
        ['Sega Naomi', 'ikaruga.bin.gz', 'ikaruga.png', 'ikaruga.mp4', 'Ikaruga', 'Treasure', '2001', 'Shooter', 'No'],
    ];
}

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Test Game List</title>';
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
    echo '<a href="test-gamelist.php" class="sidebar-nav-item active">';
    echo '<span class="sidebar-nav-icon">🎮</span><span class="sidebar-nav-label">Games (Test)</span></a>';
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
    echo '<h1 class="text-3xl" style="margin: 0;">🎮 Game Library (Test)</h1>';
    echo '<input type="text" id="searchInput" class="form-input" placeholder="🔍 Search games..." style="max-width: 300px;" oninput="filterGames()">';
    echo '</div>';
    
    echo '<div style="margin-bottom: 24px;">';
    echo '<p style="background: #2a3a4a; padding: 16px; border-radius: 8px; border-left: 4px solid #4a9eff;">';
    echo '💡 <strong>Test Page:</strong> Showing '.count($sample_games).' games from your ROM database (romsinfo.csv). ';
    echo 'Game images will display if available in the images/ folder, otherwise colorful placeholders are shown.';
    echo '</p>';
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
    
    // Game grid
    echo '<div id="gameGrid" class="game-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 24px;">';
    
    foreach ($sample_games as $game) {
        $system = $game[0];
        $filename = $game[1];
        $image = $game[2];
        $title = $game[4];
        $manufacturer = $game[5];
        $year = $game[6];
        $genre = $game[7];
        $fave = $game[8];
        
        echo '<div class="game-card" data-name="'.strtolower($title).'" data-system="'.strtolower($system).'" data-genre="'.strtolower($genre).'">';
        echo '<div class="game-card-image-container">';
        echo '<a href="#" onclick="alert(\'Launch: '.$title.'\'); return false;">';
        
        // Always show placeholder with image attempt on top
        $image_path = 'images/' . $image;
        $initial = substr($title, 0, 1);
        $colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#fa709a', '#fee140'];
        $color = $colors[ord($initial) % count($colors)];
        $fallback_color = adjustBrightness($color, -20);
        
        // Container with placeholder background
        echo '<div style="position: relative; width: 100%; height: 280px; background: linear-gradient(135deg, '.$color.' 0%, '.$fallback_color.' 100%); display: flex; align-items: center; justify-content: center; font-size: 72px; font-weight: bold; color: white; border-radius: 8px; overflow: hidden;">';
        
        // Show letter
        echo '<span style="position: absolute; z-index: 1;">'.$initial.'</span>';
        
        // Try to overlay real image if it exists
        echo '<img src="'.$image_path.'" alt="'.$title.'" ';
        echo 'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; object-position: center; z-index: 2; background: transparent;" ';
        echo 'onload="this.previousElementSibling.style.display=\'none\';" ';
        echo 'onerror="this.style.display=\'none\';">';
        
        echo '</div>';
        echo '</a>';
        
        if ($fave == 'Yes') {
            echo '<button class="game-card-favorite active" title="Remove from favorites">⭐</button>';
        }
        
        echo '<div class="game-card-overlay">';
        echo '<a href="#" onclick="alert(\'Launch: '.$title.'\'); return false;" class="btn btn-primary btn-sm">Launch</a>';
        echo '</div>';
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
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>'; // Close game grid
    echo '</div>'; // Close container
    echo '</div>'; // Close main-content
    
    // Scripts
    echo '<script>';
    echo 'let activeSystemFilter="all";let activeGenreFilter="all";let searchQuery="";';
    echo 'function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}';
    echo 'function applyFilters(){document.querySelectorAll(".game-card").forEach(c=>{const name=c.getAttribute("data-name");const sys=c.getAttribute("data-system");const genre=c.getAttribute("data-genre");const matchSearch=searchQuery===""||name.includes(searchQuery);const matchSystem=activeSystemFilter==="all"||sys.includes(activeSystemFilter);const matchGenre=activeGenreFilter==="all"||genre.includes(activeGenreFilter);c.style.display=(matchSearch&&matchSystem&&matchGenre)?"block":"none";});}';
    echo 'function filterGames(){searchQuery=document.getElementById("searchInput").value.toLowerCase();applyFilters();}';
    echo 'function filterBySystem(s){activeSystemFilter=s;applyFilters();}';
    echo 'function filterByGenre(g){activeGenreFilter=g;applyFilters();}';
    echo 'function resetFilters(){activeSystemFilter="all";activeGenreFilter="all";searchQuery="";document.getElementById("systemFilter").value="all";document.getElementById("genreFilter").value="all";document.getElementById("searchInput").value="";applyFilters();}';
    echo '</script>';
    
} else {
    // Classic UI
    include 'menu_include.php';
    echo '<section><center>';
    echo '<h1>Game Library (Test)</h1><br>';
    echo '<p style="background: yellow; padding: 10px;"><b>TEST PAGE:</b> Preview of game list with sample ROM data</p><br>';
    
    foreach ($sample_games as $game) {
        echo '<div class="box1">';
        echo '<b>'.$game[4].'</b><br>';
        echo $game[0].' - '.$game[7].' - '.$game[5].' ('.$game[6].')<br>';
        if ($game[8] == 'Yes') echo '⭐ Favourite<br>';
        echo '<a href="#" onclick="alert(\'Launch: '.$game[4].'\'); return false;" class="dropbtn">Launch</a>';
        echo '</div><br>';
    }
    
    echo '</center></section>';
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
