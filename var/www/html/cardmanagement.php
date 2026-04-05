<?php
mb_internal_encoding("UTF-8");
include 'ui_mode.php';
$mode = $_GET['mode'] ?? 'main';

// Process commands before rendering (redirects)
if (isset($_GET["command"]) && $_GET["command"] == 'delete') {
    // SECURITY: Validate file path and mode
    $deletefile = $_GET["filetodelete"] ?? '';
    $mode = $_GET['mode'] ?? '';
    
    // Whitelist allowed modes
    $allowed_modes = ['idas', 'id2', 'id3', 'fzero'];
    if (!in_array($mode, $allowed_modes)) {
        header("Location: cardmanagement.php");
        exit;
    }
    
    // Validate file path starts with expected directory
    $expected_path = '/boot/config/cards/' . $mode . '/';
    if (strpos($deletefile, $expected_path) !== 0) {
        header("Location: cardmanagement.php");
        exit;
    }
    
    // SECURITY: Use escapeshellarg for file parameter
    $command1 = 'sudo python3 /sbin/piforce/delete.py ' . escapeshellarg($deletefile);
    shell_exec($command1);
    
    $path_parts = pathinfo($deletefile);
    $phpfile = '/var/www/html/cards/' . $mode . '/' . basename($path_parts['filename']) . '.printdata.php';
    
    $command2 = 'sudo python3 /sbin/piforce/delete.py ' . escapeshellarg($phpfile);
    shell_exec($command2);
    header("Location: cardmanagement.php");
    exit;
}

if (isset($_GET["command"]) && $_GET["command"] == 'nfc') {
    // SECURITY: Validate file path and mode
    $copyfile = $_GET["filetocopy"] ?? '';
    $mode = $_GET['mode'] ?? '';
    
    // Whitelist allowed modes
    $allowed_modes = ['idas', 'id2', 'id3', 'fzero'];
    if (!in_array($mode, $allowed_modes)) {
        header("Location: cardmanagement.php");
        exit;
    }
    
    // Validate file path starts with expected directory
    $expected_path = '/boot/config/cards/' . $mode . '/';
    if (strpos($copyfile, $expected_path) !== 0) {
        header("Location: cardmanagement.php");
        exit;
    }
    
    $path_parts = pathinfo($copyfile);
    $phpfile = '/var/www/html/cards/' . $mode . '/' . basename($path_parts['filename']) . '.printdata.php';
    
    // SECURITY: Use escapeshellarg for parameters
    $command1 = 'sudo python3 /sbin/piforce/card_emulator/nfcwrite.py ' . 
                escapeshellarg($copyfile) . ' ' . 
                escapeshellarg($phpfile);
    shell_exec($command1 . ' > /dev/null 2>/dev/null &');
    header("Location: cardmanagement.php");
    exit;
}

$emumode = file_get_contents('/sbin/piforce/emumode.txt');
$nfcmode = file_get_contents('/sbin/piforce/nfcmode.txt');

// Render HTML

    echo '<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><title>WiPi Netbooter - Card Data Management</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container">';

    $cm_mode_labels = ['idas' => 'Initial D', 'id2' => 'Initial D ver.2', 'id3' => 'Initial D ver.3', 'fzero' => 'F-Zero AX'];
    $cm_bc = [['label' => 'Dashboard', 'href' => 'menu.php'], ['label' => 'Card Management', 'href' => 'cardmanagement.php?mode=main']];
    if ($mode !== 'main' && isset($cm_mode_labels[$mode])) {
        $cm_bc[] = ['label' => $cm_mode_labels[$mode]];
    } else {
        $cm_bc[1] = ['label' => 'Card Management'];
        array_pop($cm_bc);
    }
    echo breadcrumb_render($cm_bc);

    echo '<h1>'.arcade_icon('cards').' Card Data Management</h1>';


if ($mode == 'main'){
   
       echo '<p style="margin-bottom: 24px;">Select a game to manage card data</p>';
       if ($nfcmode == 'nfcon'){
           echo '<div class="card" style="margin-bottom: 24px;"><div class="card-body">';
           echo '<h3 style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">'.arcade_icon('nfc').' NFC Card Tools</h3>';
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Check NFC card contents or wipe a card ready for use. Place the card on the reader within 10 seconds after pressing a button.</p>';
           echo '<div style="display: flex; gap: 12px;">';
           echo '<a href="cardactions.php?command=nfc_check" class="btn btn-secondary" style="text-decoration: none;">Check NFC Card</a>';
           echo '<a href="cardactions.php?mode=main&command=nfcwipe" class="btn btn-danger" style="text-decoration: none;">Wipe NFC Card</a>';
           echo '</div></div></div>';
       }
       echo '<div class="grid grid-cols-4">';
       $games = [
           ['mode' => 'idas', 'img' => 'initd.png', 'title' => 'Initial D'],
           ['mode' => 'id2', 'img' => 'initd2.png', 'title' => 'Initial D ver.2'],
           ['mode' => 'id3', 'img' => 'initdv3e.png', 'title' => 'Initial D ver.3'],
           ['mode' => 'fzero', 'img' => 'FZ.png', 'title' => 'F-Zero AX']
       ];
       foreach ($games as $game) {
           echo '<a href="cardmanagement.php?mode='.$game['mode'].'" class="card card-interactive" style="text-decoration: none;">';
           echo '<div class="card-body" style="text-align: center;"><img src="images/'.$game['img'].'" style="max-width: 100%; height: auto;"><p style="margin-top: 8px; font-weight: bold;">'.$game['title'].'</p></div></a>';
       }
       echo '</div>';
   

}

if ($mode == 'idas'){
   
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">Initial D Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table class="data-table"><thead><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; 
       if($nfcmode == 'nfcon'){echo '<th>NFC</th>';} 
       echo '</tr></thead><tbody>';
   
   $path = '/boot/config/cards/idas/';
   $files = is_dir($path) ? array_diff(scandir($path), ['.', '..']) : [];
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/idas/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>'.$drivername.'</td><td>'.$carline1.' '.$carline2.'</td><td>'.$timestamp.'</td><td>'; 
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=idas&command=nfcwrite&filetocopy='.$fullfile.'" class="btn btn-secondary btn-sm">NFC Copy</a></td><td>';} 
              echo '<a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}
      else {
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>ORPHAN</td><td>NO CAR DATA</td><td>'.$timestamp.'</td><td><a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}

}

    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: var(--color-text-secondary);">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>'; // close card-body, card

}

if ($mode == 'id2'){
   
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">Initial D ver.2 Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table class="data-table"><thead><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>';
       if($nfcmode == 'nfcon'){echo '<th>NFC</th>';}
       echo '</tr></thead><tbody>';
   
   $path = '/boot/config/cards/id2/';
   $files = is_dir($path) ? array_diff(scandir($path), ['.', '..']) : [];
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/id2/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>'.$drivername.'</td><td>'.$carline1.' '.$carline2.'</td><td>'.$timestamp.'</td><td>';
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id2&command=nfcwrite&filetocopy='.$fullfile.'" class="btn btn-secondary btn-sm">NFC Copy</a></td><td>';}
              echo '<a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}
      else {
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>ORPHAN</td><td>NO CAR DATA</td><td>'.$timestamp.'</td><td><a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}

}

    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: var(--color-text-secondary);">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>';

}

if ($mode == 'id3'){
   
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">Initial D ver.3 Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table class="data-table"><thead><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>';
       if($nfcmode == 'nfcon'){echo '<th>NFC</th>';}
       echo '</tr></thead><tbody>';
   
   $path = '/boot/config/cards/id3/';
   $files = is_dir($path) ? array_diff(scandir($path), ['.', '..']) : [];
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/id3/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>'.$drivername.'</td><td>'.$carline1.' '.$carline2.'</td><td>'.$timestamp.'</td><td>';
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id3&command=nfcwrite&filetocopy='.$fullfile.'" class="btn btn-secondary btn-sm">NFC Copy</a></td><td>';}
              echo '<a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}
      else {
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>ORPHAN</td><td>NO CAR DATA</td><td>'.$timestamp.'</td><td><a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}

}

    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: var(--color-text-secondary);">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>';

}

if ($mode == 'fzero'){
   
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">F-Zero AX Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: var(--color-text-secondary);">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table class="data-table"><thead><tr><th>Driver</th><th>License</th><th>Saved</th><th>Action</th>';
       if($nfcmode == 'nfcon'){echo '<th>NFC</th>';}
       echo '</tr></thead><tbody>';
   
   $path = '/boot/config/cards/fzero/';
   $files = is_dir($path) ? array_diff(scandir($path), ['.', '..']) : [];
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/fzero/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>'.$driver.'</td><td>'.$license.'</td><td>'.$timestamp.'</td><td>';
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=fzero&command=nfcwrite&filetocopy='.$fullfile.'" class="btn btn-secondary btn-sm">NFC Copy</a></td><td>';}
              echo '<a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}
      else {
      if ($path_parts['extension'] == NULL){
          
              echo '<tr><td>ORPHAN</td><td>NO LICENSE DATA</td><td>'.$timestamp.'</td><td><a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'" class="btn btn-danger btn-sm" onclick="return confirm('Delete this card? This cannot be undone.')">Delete</a></td></tr>';
          
      }}

}

    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: var(--color-text-secondary);">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>';

}


    echo '</div></div>'; // Close main-content, container
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
