<?php
mb_internal_encoding("UTF-8");
include 'ui_mode.php';
$ui_mode = get_ui_mode();
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
    $command1 = 'sudo python /sbin/piforce/delete.py ' . escapeshellarg($deletefile);
    shell_exec($command1);
    
    $path_parts = pathinfo($deletefile);
    $phpfile = '/var/www/html/cards/' . $mode . '/' . basename($path_parts['filename']) . '.printdata.php';
    
    $command2 = 'sudo python /sbin/piforce/delete.py ' . escapeshellarg($phpfile);
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
if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Card Data Management</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header"><h2>WiPi Netbooter</h2></div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item active"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container"><div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()"><span></span><span></span><span></span></button>';
    echo '<h1>💳 Card Data Management</h1>';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<meta name="description" content="Responsive Header Nav">';
    echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    include 'menu.php';
    echo '<section><center><p>';
    if ($mode == 'main'){
        echo '<h1><a href="setup.php">Card Data Management</a></h1>';}
    else {
        echo '<h1><a href="cardmanagement.php?mode=main">Card Data Management</a></h1>';}
}

if ($mode == 'main'){
   if ($ui_mode === 'modern') {
       echo '<p style="margin-bottom: 24px;">Select a game to manage card data</p>';
       if ($nfcmode == 'nfcon'){
           echo '<div class="card" style="margin-bottom: 24px; background: #2a2a2a;"><div class="card-body">';
           echo '<h3 style="margin-bottom: 8px;">📶 NFC Card Tools</h3>';
           echo '<p style="margin-bottom: 16px; color: #aaa;">Check NFC card contents or wipe a card ready for use. Place the card on the reader within 10 seconds after pressing a button.</p>';
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
   } else {
       echo 'Please select the card reader emulator mode from the list below</br></br>';
       if ($nfcmode == 'nfcon'){
           echo 'You can use the buttons below to check the contents of an NFC card or wipe it ready for use<br><br>';
           echo 'Press the button then place the card on the reader within 10 seconds to scan it<br><br>';
           echo '<br><p><a href="cardactions.php?command=nfc_check" style="font-weight:normal" class="dropbtn">Check NFC Card</a>  <a href="cardactions.php?mode=main&command=nfcwipe" style="font-weight:normal" class="dropbtn">Wipe NFC Card</a></p><br>';
       }
       echo '<a href="cardmanagement.php?mode=idas"><img src="images/initd.png"></a></br><br>';
       echo '<a href="cardmanagement.php?mode=id2"><img src="images/initd2.png"></a></br><br>';
       echo '<a href="cardmanagement.php?mode=id3"><img src="images/initdv3e.png"></a></br><br>';
       echo '<a href="cardmanagement.php?mode=fzero"><img src="images/FZ.png"></a></br><br>';
   }

}

if ($mode == 'idas'){
   if ($ui_mode === 'modern') {
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">Initial D Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table class="center" id="options" style="width: 100%; border-collapse: collapse;"><thead><tr style="background: #2a2a2a;"><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Driver</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Car</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Saved</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Action</th>'; 
       if($nfcmode == 'nfcon'){echo '<th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">NFC</th>';} 
       echo '</tr></thead><tbody>';
   } else {
       echo '<b>Initial D Cards</b></br></br>';
       if ($nfcmode == 'nfcon'){
           echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
       } else {
           echo 'Cards can be deleted permanently using the link below</br></br>';
       }
       echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   }
   $path = '/boot/config/cards/idas/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/idas/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">'.$drivername.'</td><td style="padding: 12px;">'.$carline1.' '.$carline2.'</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;">'; 
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=idas&command=nfcwrite&filetocopy='.$fullfile.'" style="color: #4a9eff;">NFC Copy</a></td><td style="padding: 12px;">';} 
              echo '<a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>'.$drivername.'<td>'.$carline1.' '.$carline2.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=idas&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';
          }
      }}
      else {
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">ORPHAN</td><td style="padding: 12px;">NO CAR DATA</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;"><a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>ORPHAN<td>NO CAR DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';
          }
      }}

}
if ($ui_mode === 'modern') {
    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: #aaa;">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>'; // close card-body, card
} else {
    echo '</table><br><br>';
    if($nfcmode == 'nfcon'){
        echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
    }
}
}

if ($mode == 'id2'){
   if ($ui_mode === 'modern') {
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">Initial D ver.2 Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;"><thead><tr style="background: #2a2a2a;"><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Driver</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Car</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Saved</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Action</th>';
       if($nfcmode == 'nfcon'){echo '<th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">NFC</th>';}
       echo '</tr></thead><tbody>';
   } else {
       echo '<b>Initial D ver.2 Cards</b></br></br>';
       if ($nfcmode == 'nfcon'){
           echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
       } else {
           echo 'Cards can be deleted permanently using the link below</br></br>';
       }
       echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   }
   $path = '/boot/config/cards/id2/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/id2/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">'.$drivername.'</td><td style="padding: 12px;">'.$carline1.' '.$carline2.'</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;">';
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id2&command=nfcwrite&filetocopy='.$fullfile.'" style="color: #4a9eff;">NFC Copy</a></td><td style="padding: 12px;">';}
              echo '<a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>'.$drivername.'<td>'.$carline1.' '.$carline2.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id2&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';
          }
      }}
      else {
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">ORPHAN</td><td style="padding: 12px;">NO CAR DATA</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;"><a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>ORPHAN<td>NO CAR DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';
          }
      }}

}
if ($ui_mode === 'modern') {
    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: #aaa;">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>';
} else {
    echo '</table><br><br>';
    if($nfcmode == 'nfcon'){
        echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
    }
}
}

if ($mode == 'id3'){
   if ($ui_mode === 'modern') {
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">Initial D ver.3 Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;"><thead><tr style="background: #2a2a2a;"><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Driver</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Car</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Saved</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Action</th>';
       if($nfcmode == 'nfcon'){echo '<th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">NFC</th>';}
       echo '</tr></thead><tbody>';
   } else {
       echo '<b>Initial D ver.3 Cards</b></br></br>';
       if ($nfcmode == 'nfcon'){
           echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
       } else {
           echo 'Cards can be deleted permanently using the link below</br></br>';
       }
       echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   }
   $path = '/boot/config/cards/id3/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/id3/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">'.$drivername.'</td><td style="padding: 12px;">'.$carline1.' '.$carline2.'</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;">';
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id3&command=nfcwrite&filetocopy='.$fullfile.'" style="color: #4a9eff;">NFC Copy</a></td><td style="padding: 12px;">';}
              echo '<a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>'.$drivername.'<td>'.$carline1.' '.$carline2.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id3&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';
          }
      }}
      else {
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">ORPHAN</td><td style="padding: 12px;">NO CAR DATA</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;"><a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>ORPHAN<td>NO CAR DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';
          }
      }}

}
if ($ui_mode === 'modern') {
    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: #aaa;">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>';
} else {
    echo '</table><br><br>';
    if($nfcmode == 'nfcon'){
        echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
    }
}
}

if ($mode == 'fzero'){
   if ($ui_mode === 'modern') {
       echo '<div class="card"><div class="card-body">';
       echo '<h2 style="margin-bottom: 8px;">F-Zero AX Cards</h2>';
       if ($nfcmode == 'nfcon'){
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently or copied to NFC card</p>';
       } else {
           echo '<p style="margin-bottom: 16px; color: #aaa;">Cards can be deleted permanently using the link below</p>';
       }
       echo '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;"><thead><tr style="background: #2a2a2a;"><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Driver</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">License</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Saved</th><th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">Action</th>';
       if($nfcmode == 'nfcon'){echo '<th style="padding: 12px; text-align: left; border-bottom: 2px solid #444;">NFC</th>';}
       echo '</tr></thead><tbody>';
   } else {
       echo '<b>F-Zero AX Cards</b></br></br>';
       if ($nfcmode == 'nfcon'){
           echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
       } else {
           echo 'Cards can be deleted permanently using the link below</br></br>';
       }
       echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>License</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   }
   $path = '/boot/config/cards/fzero/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/fzero/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">'.$driver.'</td><td style="padding: 12px;">'.$license.'</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;">';
              if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=fzero&command=nfcwrite&filetocopy='.$fullfile.'" style="color: #4a9eff;">NFC Copy</a></td><td style="padding: 12px;">';}
              echo '<a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>'.$driver.'<td>'.$license.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id3&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';
          }
      }}
      else {
      if ($path_parts['extension'] == NULL){
          if ($ui_mode === 'modern') {
              echo '<tr style="border-bottom: 1px solid #333;"><td style="padding: 12px;">ORPHAN</td><td style="padding: 12px;">NO LICENSE DATA</td><td style="padding: 12px;">'.$timestamp.'</td><td style="padding: 12px;"><a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'" style="color: #ff4a4a;">delete</a></td></tr>';
          } else {
              echo '<tr><td>ORPHAN<td>NO LICENSE DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';
          }
      }}

}
if ($ui_mode === 'modern') {
    echo '</tbody></table></div>';
    if($nfcmode == 'nfcon'){
        echo '<p style="margin-top: 16px; font-size: 0.9em; color: #aaa;">To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader. The card will be wiped and the reader will confirm a successful write with two beeps.</p>';
    }
    echo '</div></div>';
} else {
    echo '</table><br><br>';
    if($nfcmode == 'nfcon'){
        echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
    }
}
}

if ($ui_mode === 'modern') {
    echo '</div></div>'; // Close main-content, container
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}</script>';
    echo '</body></html>';
} else {
    echo '</p></center></section></body></html>';
}
?>
