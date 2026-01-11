<?php

// Load UI mode helper
include_once 'ui_mode.php';
$ui_mode = get_ui_mode();

// Helper function to render game card (both Modern and Classic UI)
function render_game_card($row, $ui_mode, $menumode, $filtertype = null, $value = null, $display = null) {
    $system = $row[0];
    $filename = $row[1];
    $image = $row[2];
    $gamename = $row[4];
    $mapping = $row[14];
    $ffb = $row[15];
    $fave = $row[13];
    
    if ($ui_mode === 'modern') {
        // Modern UI: Game card
        echo '<div class="game-card" data-name="'.strtolower($gamename).'" data-system="'.strtolower($system).'">';
        echo '<div class="game-card-image-container">';
        
        if ($menumode == 'advanced'){
            if ($filtertype != null) {
                echo '<a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'">';
            } elseif ($display != null) {
                echo '<a href="gamelist.php?filename='.$filename.'&display='.$display.'">';
            } else {
                echo '<a href="gamelist.php?filename='.$filename.'">';
            }
        } else {
            echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">';
        }
        
        echo '<img src="images/'.$image.'" alt="'.$gamename.'" class="game-card-image" loading="lazy">';
        echo '</a>';
        
        if ($fave == "Yes"){
            echo '<button class="game-card-favorite active" title="Remove from favorites">⭐</button>';
        }
        
        echo '<div class="game-card-overlay">';
        if ($menumode == 'advanced'){
            if ($filtertype != null) {
                echo '<a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'" class="btn btn-primary btn-sm">Details</a>';
            } elseif ($display != null) {
                echo '<a href="gamelist.php?filename='.$filename.'&display='.$display.'" class="btn btn-primary btn-sm">Details</a>';
            } else {
                echo '<a href="gamelist.php?filename='.$filename.'" class="btn btn-primary btn-sm">Details</a>';
            }
        } else {
            echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'" class="btn btn-primary btn-sm">Launch</a>';
        }
        echo '</div>';
        
        echo '</div>';
        echo '<div class="game-card-content">';
        echo '<h3 class="game-card-title">'.$gamename.'</h3>';
        echo '<span class="game-card-system-badge '.strtolower($system).'">'.$system.'</span>';
        echo '</div>';
        echo '</div>';
    } else {
        // Classic UI
        echo '<div class="box1">';
        echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
        if ($menumode == 'advanced'){
            if ($filtertype != null) {
                echo '<a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'"><img src="images/'.$image.'"></a></br></br>';
                if ($fave == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                else {echo '<b><a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b></br>';}
            } elseif ($display != null) {
                echo '<a href="gamelist.php?filename='.$filename.'&display='.$display.'"><img src="images/'.$image.'"></a></br></br>';
                if ($fave == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$filename.'&display='.$display.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                else {echo '<b><a href="gamelist.php?filename='.$filename.'&display='.$display.'">'.$gamename.'</a></b></br>';}
            } else {
                echo '<a href="gamelist.php?filename='.$filename.'"><img src="images/'.$image.'"></a></br></br>';
                if ($fave == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$filename.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div>';}
                else {echo '<b><a href="gamelist.php?filename='.$filename.'">'.$gamename.'</a></b><br>';}
            }
        } else {
            echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
            if ($fave == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br></br>';}
            else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a><br>';}
        }
        echo '</div><br>';
    }
}

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';

// Load CSS based on UI mode
load_ui_styles();

// Only include old menu for classic UI
if ($ui_mode === 'classic') {
    include 'menu_include.php';
}

?>

<!DOCTYPE html>
<html>
<body>

<?php

$display = isset($_GET['display']) ? $_GET['display'] : null;
$filtertype = isset($_GET['filter']) ? $_GET['filter'] : null;
$value = isset($_GET['value']) ? $_GET['value'] : null;
$filename = isset($_GET['filename']) ? $_GET['filename'] : null;
$csvfile = 'csv/romsinfo.csv';
$path = '/boot/roms';
$menumode = file_get_contents('/sbin/piforce/menumode.txt');
$openmode = file_get_contents('/sbin/piforce/openmode.txt');
$soundmode = file_get_contents('/sbin/piforce/soundmode.txt');
$navmode = file_get_contents('/sbin/piforce/navmode.txt');
$ffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');

// Modern UI wrapper
if ($ui_mode === 'modern') {
    echo '<button class="burger-menu" id="burgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu"><span></span><span></span><span></span></button>';
    echo '<div class="sidebar-nav" id="sidebarNav">';
    echo '<nav>';
    echo '<a href="gamelist.php?display=all" class="sidebar-nav-item active">';
    echo '<span class="sidebar-nav-icon">🎮</span><span class="sidebar-nav-label">Games</span></a>';
    echo '<a href="dimms.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">💾</span><span class="sidebar-nav-label">NetDIMMs</span></a>';
    echo '<a href="setup.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">⚙️</span><span class="sidebar-nav-label">Setup</span></a>';
    echo '<a href="menu.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">📋</span><span class="sidebar-nav-label">Options</span></a>';
    echo '<a href="ui-mode-switcher.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎨</span><span class="sidebar-nav-label">UI Mode</span></a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    echo '<div class="main-content">';
} else {
    echo '<section><center>';
    echo '<div>';
}

if ($navmode == "navon"){
echo '<button onclick="topFunction()" id="rtnBtn" title="Go to top"><img src="img/rtn.png" /></button>';
}

if ($display == 'all'){

if ($ui_mode === 'modern') {
    // Modern UI: Search bar and filter chips
    echo '<div class="container" style="padding: 20px;">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 24px;">';
    echo '<h1 class="text-3xl" style="margin: 0;">Game Library</h1>';
    echo '<input type="text" id="searchInput" class="form-input" placeholder="🔍 Search games..." style="max-width: 300px;" oninput="filterGames()">';
    echo '</div>';
    
    echo '<div class="flex" style="gap: 8px; flex-wrap: wrap; margin-bottom: 24px;">';
    echo '<div class="dropdown" style="position: relative;">';
    echo '<button onclick="SystemFunction()" class="btn btn-secondary">System ▾</button>';
    echo '<div id="SystemDropdown" class="dropdown-content">';
    $unique_ids = array();
    $f = fopen('csv/romsinfo.csv', 'r');
    $headers = ($row = fgetcsv($f));
    while ($row = fgetcsv($f)) {
        $unique_ids[$row[0]] = true;
    }
    ksort($unique_ids);
    $categories = array_keys($unique_ids);
    foreach($categories as $category => $value) {
        echo '<a href="/gamelist.php?filter=system&value='.$value.'">'.$value.'</a>';
    }
    fclose($f);
    echo '</div></div>';
    
    echo '<div class="dropdown" style="position: relative;">';
    echo '<button onclick="GenreFunction()" class="btn btn-secondary">Genre ▾</button>';
    echo '<div id="GenreDropdown" class="dropdown-content">';
    $unique_ids = array();
    $f = fopen('csv/romsinfo.csv', 'r');
    $headers = ($row = fgetcsv($f));
    while ($row = fgetcsv($f)) {
        $unique_ids[$row[8]] = true;
    }
    ksort($unique_ids);
    $categories = array_keys($unique_ids);
    foreach($categories as $category => $value) {
        echo '<a href="/gamelist.php?filter=genre&value='.$value.'">'.$value.'</a>';
    }
    fclose($f);
    echo '</div></div>';
    
    echo '<div class="dropdown" style="position: relative;">';
    echo '<button onclick="OrientationFunction()" class="btn btn-secondary">Orientation ▾</button>';
    echo '<div id="OrientationDropdown" class="dropdown-content">';
    $unique_ids = array();
    $f = fopen('csv/romsinfo.csv', 'r');
    $headers = ($row = fgetcsv($f));
    while ($row = fgetcsv($f)) {
        $unique_ids[$row[10]] = true;
    }
    ksort($unique_ids);
    $categories = array_keys($unique_ids);
    foreach($categories as $category => $value) {
        echo '<a href="/gamelist.php?filter=orientation&value='.$value.'">'.$value.'</a>';
    }
    fclose($f);
    echo '</div></div>';
    
    echo '<div class="dropdown" style="position: relative;">';
    echo '<button onclick="ControlFunction()" class="btn btn-secondary">Controls ▾</button>';
    echo '<div id="ControlDropdown" class="dropdown-content">';
    $unique_ids = array();
    $f = fopen('csv/romsinfo.csv', 'r');
    $headers = ($row = fgetcsv($f));
    while ($row = fgetcsv($f)) {
        $unique_ids[$row[11]] = true;
    }
    ksort($unique_ids);
    $categories = array_keys($unique_ids);
    foreach($categories as $category => $value) {
        echo '<a href="/gamelist.php?filter=controls&value='.$value.'">'.$value.'</a>';
    }
    fclose($f);
    echo '</div></div>';
    
    echo '<a href="gamelist.php?display=faves" class="btn btn-secondary">⭐ Favorites</a>';
    echo '</div>';
    
    // Alphabet navigation for modern UI - Sticky
    echo '<div id="alphabetNav" class="alphabet-nav" style="position: sticky; top: 72px; z-index: 100; background: var(--color-background); padding: 16px 0; margin-bottom: 24px; border-bottom: 2px solid var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
    echo '<div class="flex" style="gap: 4px; flex-wrap: wrap; font-size: 14px; justify-content: center;">';
    $files = array_values(array_diff(scandir($path), array('.', '..')));
    $games_array = array();
    $f = fopen('csv/romsinfo.csv', 'r');
    while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
            if ((in_array($row[1], $files)) and ($row[12] == "Yes")){
                $games_array[strtoupper(substr($row[4],0,1))] = true;
            }
        }
    }
    $alphabetUpper = range('A', 'Z');
    $letters = array_keys($games_array);
    foreach($alphabetUpper as $letter => $value) {
        if (in_array($value, $letters)){
            echo '<a href="#anchor'.$value.'" class="alphabet-link badge badge-primary" data-letter="'.$value.'" style="cursor: pointer; text-decoration: none; transition: all 0.2s;">'.$value.'</a>';
        } else {
            echo '<span class="badge" style="opacity: 0.3;">'.$value.'</span>';
        }
    }
    fclose($f);
    echo '</div></div>';
    
    echo '<div class="grid grid-cols-4" id="gameGrid">';
} else {
    // Classic UI
?>

<div class="dropdown">
  <button onclick="SystemFunction()" class="dropbtn">System</button>
  <div id="SystemDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[0]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=system&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>
 </div>
</div>

<div class="dropdown">
  <button onclick="GenreFunction()" class="dropbtn">Genre</button>
  <div id="GenreDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[8]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=genre&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>

 </div>
</div>

<div class="dropdown">
  <button onclick="OrientationFunction()" class="dropbtn">Orientation</button>
  <div id="OrientationDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[10]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=orientation&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>

 </div>
</div>

<div class="dropdown">
  <button onclick="ControlFunction()" class="dropbtn">Controls</button>
  <div id="ControlDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[11]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=controls&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>

 </div>
</div>

<br><br>

<?php

$files = array_values(array_diff(scandir($path), array('.', '..')));
$games_array = array();
$f = fopen('csv/romsinfo.csv', 'r');
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes")){
                  $games_array[strtoupper(substr($row[4],0,1))] = true;

  }
}
}

$alphabetUpper = range('A', 'Z');
$letters = array_keys($games_array);
 foreach($alphabetUpper as $letter => $value)
  {
    if (in_array($value, $letters)){echo '<li><a href="#anchor'.$value.'" class="scrollLink">'.$value.'</a></li>';}
    else{echo '<li>'.$value.'</li>';}
  }
fclose($f);

echo '<br><br></div>';
echo '<a id="anchorTOP" class="anchors"></a>';
}


   $lastname = 'aaaa';
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes")){
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  $fave = $row[13];
                  $lastletter = strtoupper(substr($lastname,0,1));
                  $thisletter = strtoupper(substr($gamename,0,1));
                  
                  if ($ui_mode === 'modern') {
                      // Modern UI: Game card
                      if (strcmp($lastletter, $thisletter) < 0 ){
                           echo '<a id="anchor'.$thisletter.'" class="anchors"></a>';
                      }
                      echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
                      echo '<div class="game-card" data-name="'.strtolower($gamename).'" data-system="'.strtolower($system).'">';
                      echo '<div class="game-card-image-container">';
                      
                      if ($menumode == 'advanced'){
                          echo '<a href="gamelist.php?filename='.$filename.'">';
                      } else {
                          echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">';
                      }
                      
                      echo '<img src="images/'.$image.'" alt="'.$gamename.'" class="game-card-image" loading="lazy">';
                      echo '</a>';
                      
                      if ($fave == "Yes"){
                          echo '<button class="game-card-favorite active" title="Remove from favorites">⭐</button>';
                      }
                      
                      echo '<div class="game-card-overlay">';
                      if ($menumode == 'advanced'){
                          echo '<a href="gamelist.php?filename='.$filename.'" class="btn btn-primary btn-sm">Details</a>';
                      } else {
                          echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'" class="btn btn-primary btn-sm">Launch</a>';
                      }
                      echo '</div>';
                      
                      echo '</div>';
                      echo '<div class="game-card-content">';
                      echo '<h3 class="game-card-title">'.$gamename.'</h3>';
                      echo '<span class="game-card-system-badge '.strtolower($system).'">'.$system.'</span>';
                      echo '</div>';
                      echo '</div>';
                  } else {
                      // Classic UI
                      echo '<div class="box1">';
                      if (strcmp($lastletter, $thisletter) < 0 ){
                           echo '<a id="anchor'.$thisletter.'" class="anchors"></a>';
                      }
                      echo '<a id="anchor'.$gamename.'" class="anchors"></a>';                  
                      if ($menumode == 'advanced'){
                      echo '<a href="gamelist.php?filename='.$row[1].'"><img src="images/'.$image.'"></a></br></br>';
                      if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div>';}
                      else {echo '<b><a href="gamelist.php?filename='.$row[1].'">'.$gamename.'</a></b><br>';}}
                      else {
                      echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                      if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br></br>';}
                      else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a><br>';}}
                      echo '</div><br>';
                  }
                  
                  $lastname = $gamename;
                  break;
              }
         }
     }
   fclose($f);

   if ($ui_mode === 'modern') {
       echo '</div>'; // Close grid
       echo '</div>'; // Close container
   }
   
   if ($i == 0){
      if ($ui_mode === 'modern') {
          echo '<div class="container" style="padding: 40px; text-align: center;">';
          echo '<div class="empty-state">';
          echo '<div class="empty-state-icon">🎮</div>';
          echo '<h2>No Games Found</h2>';
          echo '<p>No games are currently available. Add ROM files to get started.</p>';
          echo '<a href="gamelist.php?display=all" class="btn btn-primary">View All Games</a>';
          echo '</div></div>';
      } else {
          echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
      }
   }

}

if ($filename !== null && $display != "all") {
    $f = fopen($csvfile, "r");
    while ($row = fgetcsv($f)) {
        if ($row[1] == $filename) {
            $system = $row[0];
            $image = $row[2];
            $video = $row[3];
            $gamename = $row[4];
            $manufacturer = $row[6];
            $year = $row[7];
            $genre = $row[8];
            $rating = $row[9];
            $orientation = $row[10];
            $controls = $row[11];
            $enabled = $row[12];
            $fave = $row[13];
            $mapping = $row[14];
            $ffb = $row[15];
            if ($filtertype != null){
                 echo '<a href="gamelist.php?filter='.$filtertype.'&value='.$value.'#anchor'.$gamename.'"><img src="images/'.$image.'"></a><br><br>';
                 echo '<h1><a href="gamelist.php?filter='.$filtertype.'&value='.$value.'#anchor'.$gamename.'">'.$gamename.'</a><h1>';}
            elseif ($display != null){
                 echo '<a href="gamelist.php?display='.$display.'#anchor'.$gamename.'"><img src="images/'.$image.'"></a><br><br>';
                 echo '<h1><a href="gamelist.php?display='.$display.'#anchor'.$gamename.'">'.$gamename.'</a><h1>';}
            else {
                 echo '<a href="gamelist.php?display=all#anchor'.$gamename.'"><img src="images/'.$image.'"></a><br><br>';
                 echo '<h1><a href="gamelist.php?display=all#anchor'.$gamename.'">'.$gamename.'</a><h1>';
            }
            echo '<h1><form action="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'" method="post"><input type="submit" class="bigdropbtn" value="Launch Game"></form></h1>';
            if ($video !== ''){
              if ($soundmode == "soundoff"){
                echo '<video height=240 width=320 controls autoplay playsinline muted loop id="myVideo"><source src="/videos/'.$video.'" type="video/mp4"></video><br><br>';}
              else {
                echo '<video height=240 width=320 controls autoplay playsinline loop id="myVideo"><source src="/videos/'.$video.'" type="video/mp4"></video><br><br>';}
            }
            echo '<table id="gameinfo" style="width:100%"><tr><td><b>System</b></td><td>'.$system.'</td><td><b>Manufacturer</b></td><td>'.$manufacturer.'</td></tr><tr><td><b>Year</b></td><td>'.$year.'</td><td><b>Genre</b></td><td>'.$genre.'</td></tr><tr><td><b>Controls</b></td><td>'.$controls.'</td><td><b>Orientation</b></td><td>'.$orientation.'</td></tr>';
            if ($openmode == 'openon'){
                // SECURITY: Static command with no user input
                $command = 'sudo python /sbin/piforce/devicelist.py';
                shell_exec($command);
                include 'devicelist.php';
                echo '<tr><td><b>Mapping</b></td><td>'.$mapping.'</td><td><b>Controllers</b></td><td>'.$enableddevices.'</td></tr>';}
            echo '</table><br>';
            break;
        }
    }
   fclose($f);
if ($fave !== 'Yes'){
echo '<a href="updatecsvfave.php?rom='.$filename.'&fave=Yes">Add to Favourites</a>';
} else{
echo '<a href="updatecsvfave.php?rom='.$filename.'&fave=No">Remove from Favourites</a>';
}}


if ($filtertype == "genre" && $filename == null) {
   if ($ui_mode === 'modern') {
       echo '<div class="container" style="padding: 20px;">';
       echo '<div class="flex" style="align-items: center; margin-bottom: 24px; gap: 16px;">';
       echo '<a href="gamelist.php?display=all" class="btn btn-secondary">← Back</a>';
       echo '<h1 class="text-3xl" style="margin: 0;">'.$value.' Games</h1>';
       echo '</div>';
       echo '<div class="grid grid-cols-4" id="gameGrid">';
   } else {
       echo '<div><a href="gamelist.php?display=all"></div><h1>'.$value.' Games</h1></a></h1></div><br>';
   }
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[8] == $value)){
                  $i++;
                  echo '<a id="anchor'.$row[4].'" class="anchors"></a>';
                  render_game_card($row, $ui_mode, $menumode, $filtertype, $value, null);
                  break;
              }
         }
     }
   fclose($f);

   if ($ui_mode === 'modern') {
       echo '</div>'; // Close grid
       echo '</div>'; // Close container
   }
   
   if ($i == 0){
      if ($ui_mode === 'modern') {
          echo '<div class="container" style="padding: 40px; text-align: center;">';
          echo '<div class="empty-state">';
          echo '<div class="empty-state-icon">🎮</div>';
          echo '<h2>No '.$value.' Games Found</h2>';
          echo '<p>No games found in this genre.</p>';
          echo '<a href="gamelist.php?display=all" class="btn btn-primary">View All Games</a>';
          echo '</div></div>';
      } else {
          echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
      }
   }
}

if ($filtertype == "system" && $filename == null) {
   if ($ui_mode === 'modern') {
       echo '<div class="container" style="padding: 20px;">';
       echo '<div class="flex" style="align-items: center; margin-bottom: 24px; gap: 16px;">';
       echo '<a href="gamelist.php?display=all" class="btn btn-secondary">← Back</a>';
       echo '<img src="img/'.$value.'.png" style="height: 48px;" alt="'.$value.'">';
       echo '<h1 class="text-3xl" style="margin: 0;">'.$value.' Games</h1>';
       echo '</div>';
       echo '<div class="grid grid-cols-4" id="gameGrid">';
   } else {
       echo '<a href="gamelist.php?display=all"><img src="img/'.$value.'.png"></a><br><br>';
   }
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[0] == $value)){
                  $i++;
                  echo '<a id="anchor'.$row[4].'" class="anchors"></a>';
                  render_game_card($row, $ui_mode, $menumode, $filtertype, $value, null);
                  break;
              }
         }
     }
   fclose($f);

   if ($ui_mode === 'modern') {
       echo '</div>'; // Close grid
       echo '</div>'; // Close container
   }
   
   if ($i == 0){
      if ($ui_mode === 'modern') {
          echo '<div class="container" style="padding: 40px; text-align: center;">';
          echo '<div class="empty-state">';
          echo '<div class="empty-state-icon">🎮</div>';
          echo '<h2>No '.$value.' Games Found</h2>';
          echo '<p>No games found for this system.</p>';
          echo '<a href="gamelist.php?display=all" class="btn btn-primary">View All Games</a>';
          echo '</div></div>';
      } else {
          echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
      }
   }
}

if ($filtertype == "orientation" && $filename == null) {
   if ($ui_mode === 'modern') {
       echo '<div class="container" style="padding: 20px;">';
       echo '<div class="flex" style="align-items: center; margin-bottom: 24px; gap: 16px;">';
       echo '<a href="gamelist.php?display=all" class="btn btn-secondary">← Back</a>';
       echo '<h1 class="text-3xl" style="margin: 0;">'.$value.' Games</h1>';
       echo '</div>';
       echo '<div class="grid grid-cols-4" id="gameGrid">';
   } else {
       echo '<div><a href="gamelist.php?display=all"></div><h1>'.$value.' Games</h1></a></div><br>';
   }
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[10] == $value)){
                  $i++;
                  echo '<a id="anchor'.$row[4].'" class="anchors"></a>';
                  render_game_card($row, $ui_mode, $menumode, $filtertype, $value, null);
                  break;
              }
         }
     }
   fclose($f);

   if ($ui_mode === 'modern') {
       echo '</div>'; // Close grid
       echo '</div>'; // Close container
   }
   
   if ($i == 0){
      if ($ui_mode === 'modern') {
          echo '<div class="container" style="padding: 40px; text-align: center;">';
          echo '<div class="empty-state">';
          echo '<div class="empty-state-icon">🎮</div>';
          echo '<h2>No '.$value.' Games Found</h2>';
          echo '<p>No games found with this orientation.</p>';
          echo '<a href="gamelist.php?display=all" class="btn btn-primary">View All Games</a>';
          echo '</div></div>';
      } else {
          echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
      }
   }
}

if ($filtertype == "controls" && $filename == null) {
   if ($ui_mode === 'modern') {
       echo '<div class="container" style="padding: 20px;">';
       echo '<div class="flex" style="align-items: center; margin-bottom: 24px; gap: 16px;">';
       echo '<a href="gamelist.php?display=all" class="btn btn-secondary">← Back</a>';
       echo '<h1 class="text-3xl" style="margin: 0;">Games with '.strtolower($value).' controls</h1>';
       echo '</div>';
       echo '<div class="grid grid-cols-4" id="gameGrid">';
   } else {
       echo '<div><a href="gamelist.php?display=all"></div><h1>Games with '.strtolower($value).' '.$filtertype.'</h1></a></div><br>';
   }
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[11] == $value)){
                  $i++;
                  echo '<a id="anchor'.$row[4].'" class="anchors"></a>';
                  render_game_card($row, $ui_mode, $menumode, $filtertype, $value, null);
                  break;
              }
         }
     }
   fclose($f);

   if ($ui_mode === 'modern') {
       echo '</div>'; // Close grid
       echo '</div>'; // Close container
   }
   
   if ($i == 0){
      if ($ui_mode === 'modern') {
          echo '<div class="container" style="padding: 40px; text-align: center;">';
          echo '<div class="empty-state">';
          echo '<div class="empty-state-icon">🎮</div>';
          echo '<h2>No Games Found</h2>';
          echo '<p>No games found with '.strtolower($value).' controls.</p>';
          echo '<a href="gamelist.php?display=all" class="btn btn-primary">View All Games</a>';
          echo '</div></div>';
      } else {
          echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
      }
   }
}

if ($display == "faves" && $filename == null) {
   if ($ui_mode === 'modern') {
       echo '<div class="container" style="padding: 20px;">';
       echo '<div class="flex" style="align-items: center; margin-bottom: 24px; gap: 16px;">';
       echo '<a href="gamelist.php?display=all" class="btn btn-secondary">← Back</a>';
       echo '<h1 class="text-3xl" style="margin: 0;">⭐ Favourite Games</h1>';
       echo '</div>';
       echo '<div class="grid grid-cols-4" id="gameGrid">';
   } else {
       echo '<div><a href="gamelist.php?display=all"></div><h1>Favourite Games</h1></a></div><br>';
   }
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[13] == "Yes")){
                  $i++;
                  echo '<a id="anchor'.$row[4].'" class="anchors"></a>';
                  render_game_card($row, $ui_mode, $menumode, null, null, $display);
                  break;
              }
         }
     }
   fclose($f);

   if ($ui_mode === 'modern') {
       echo '</div>'; // Close grid
       echo '</div>'; // Close container
   }
   
   if ($i == 0){
      if ($ui_mode === 'modern') {
          echo '<div class="container" style="padding: 40px; text-align: center;">';
          echo '<div class="empty-state">';
          echo '<div class="empty-state-icon">⭐</div>';
          echo '<h2>No Favourites</h2>';
          echo '<p>You haven\'t added any games to your favourites yet.</p>';
          echo '<a href="gamelist.php?display=all" class="btn btn-primary">Browse Games</a>';
          echo '</div></div>';
      } else {
          echo '<div><a href="gamelist.php?display=all"></div>NO FAVOURITES FOUND</a></div>';
      }
   }
}


if ($ui_mode === 'modern') {
    echo '</div>'; // Close main-content
} else {
    echo '</div>';
}

?>

<script>

function SystemFunction() {
  document.getElementById("SystemDropdown").classList.toggle("show");
  document.getElementById("GenreDropdown").classList.remove("show");
  document.getElementById("OrientationDropdown").classList.remove("show");
  document.getElementById("ControlDropdown").classList.remove("show");
}

function GenreFunction() {
  document.getElementById("SystemDropdown").classList.remove("show");
  document.getElementById("GenreDropdown").classList.toggle("show");
  document.getElementById("OrientationDropdown").classList.remove("show");
  document.getElementById("ControlDropdown").classList.remove("show");

}

function OrientationFunction() {
  document.getElementById("SystemDropdown").classList.remove("show");
  document.getElementById("GenreDropdown").classList.remove("show");
  document.getElementById("OrientationDropdown").classList.toggle("show");
  document.getElementById("ControlDropdown").classList.remove("show");

}

function ControlFunction() {
  document.getElementById("SystemDropdown").classList.remove("show");
  document.getElementById("GenreDropdown").classList.remove("show");
  document.getElementById("OrientationDropdown").classList.remove("show");
  document.getElementById("ControlDropdown").classList.toggle("show");

}

rtnbutton = document.getElementById("rtnBtn");
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 350 || document.documentElement.scrollTop > 350) {
    rtnbutton.style.display = "block";
  } else {
    rtnbutton.style.display = "none";
  }
}

function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

<?php if ($ui_mode === 'modern') { ?>
// Sidebar toggle for Modern UI
function toggleSidebar() {
  const sidebar = document.getElementById('sidebarNav');
  const overlay = document.getElementById('sidebarOverlay');
  const burger = document.getElementById('burgerBtn');
  sidebar.classList.toggle('open');
  overlay.classList.toggle('show');
  burger.classList.toggle('open');
}

// Search functionality
function filterGames() {
  const searchInput = document.getElementById('searchInput');
  const filter = searchInput.value.toLowerCase();
  const gameCards = document.querySelectorAll('.game-card');
  
  gameCards.forEach(card => {
    const gameName = card.getAttribute('data-name');
    const system = card.getAttribute('data-system');
    
    if (gameName.includes(filter) || system.includes(filter)) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}

// Smooth scroll for alphabet navigation
document.addEventListener('DOMContentLoaded', function() {
  const alphabetLinks = document.querySelectorAll('.alphabet-link');
  
  alphabetLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const targetId = this.getAttribute('href');
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        // Highlight active letter
        alphabetLinks.forEach(l => l.style.transform = 'scale(1)');
        this.style.transform = 'scale(1.2)';
        
        // Smooth scroll with offset for sticky nav
        const offsetTop = targetElement.offsetTop - 150;
        window.scrollTo({
          top: offsetTop,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // Add hover effect
  alphabetLinks.forEach(link => {
    link.addEventListener('mouseenter', function() {
      this.style.transform = 'scale(1.15)';
    });
    link.addEventListener('mouseleave', function() {
      this.style.transform = 'scale(1)';
    });
  });
});
<?php } ?>

</script>

</p><center>
     
</body>
</html>
