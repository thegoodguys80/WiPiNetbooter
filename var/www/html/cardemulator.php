<?php
mb_internal_encoding("UTF-8");
include 'ui_mode.php';
$ui_mode = get_ui_mode();
$mode = $_GET['mode'] ?? 'main';
$emumode = file_get_contents('/sbin/piforce/emumode.txt');

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Card Emulator</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header"><h2>WiPi Netbooter</h2></div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
    <a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item active"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container"><div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()"><span></span><span></span><span></span></button>';
    echo '<h1>💳 Card Reader Emulator</h1>';
    
    if ($mode == 'main') {
        echo '<p style="margin-bottom: 24px;">Select a card reader emulator mode</p>';
        echo '<div class="grid grid-cols-3">';
        $games = [
            ['mode' => 'idas', 'img' => 'initd.png', 'title' => 'Initial D'],
            ['mode' => 'id2', 'img' => 'initd2.png', 'title' => 'Initial D ver.2'],
            ['mode' => 'id3', 'img' => 'initdv3e.png', 'title' => 'Initial D ver.3'],
            ['mode' => 'wmmt', 'img' => 'wmmt.gif', 'title' => 'Wangan Midnight'],
            ['mode' => 'mkgp', 'img' => 'mkgp.png', 'title' => 'Mario Kart GP'],
            ['mode' => 'fzero', 'img' => 'FZ.png', 'title' => 'F-Zero AX']
        ];
        foreach ($games as $game) {
            echo '<a href="cardemulator.php?mode='.$game['mode'].'" class="card card-interactive" style="text-decoration: none;">';
            echo '<div class="card-body" style="text-align: center;"><img src="images/'.$game['img'].'" style="max-width: 100%; height: auto;"><p style="margin-top: 8px; font-weight: bold;">'.$game['title'].'</p></div></a>';
        }
        echo '</div>';
    } else {
        // Modern UI for card selection modes
        $modeTitles = [
            'idas' => 'Initial D',
            'id2' => 'Initial D ver.2',
            'id3' => 'Initial D ver.3',
            'wmmt' => 'Wangan Midnight',
            'mkgp' => 'Mario Kart GP/GP2',
            'fzero' => 'F-Zero AX'
        ];
        echo '<div class="card"><div class="card-body">';
        echo '<h2 style="margin-bottom: 8px;">' . ($modeTitles[$mode] ?? 'Card Selection') . ' <span class="badge badge-primary" style="font-size: 0.8em;">' . $emumode . '</span></h2>';
        echo '<p style="margin-bottom: 16px;">';
        if ($emumode == 'auto') {
            echo 'Select a card or purchase a new one in the game';
        } else {
            echo 'Select a card or create a new one using the form below. The emulator will stay running until a new card is launched or the Pi powers off.';
        }
        echo '</p>';
        echo '<div style="display: flex; flex-direction: column; gap: 16px; align-items: center;">';
    }
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<meta name="description" content="Responsive Header Nav">';
    echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    include 'menu_include.php';
    echo '<section><center><p>';
    if ($mode == 'main'){
        echo '<h1><a href="setup.php">Card Reader Emulator</a></h1>';}
    else {
        echo '<h1><a href="cardemulator.php?mode=main">Card Reader Emulator</a></h1>';}
    
    if ($mode == 'main'){
       echo 'Please select the card reader emulator mode from the list below</br></br>';
       echo '<a href="cardemulator.php?mode=idas"><img src="images/initd.png"></a></br><br>';
       echo '<a href="cardemulator.php?mode=id2"><img src="images/initd2.png"></a></br><br>';
       echo '<a href="cardemulator.php?mode=id3"><img src="images/initdv3e.png"></a></br><br>';
       echo '<a href="cardemulator.php?mode=wmmt"><img src="images/wmmt.gif"></a></br><br>';
       echo '<a href="cardemulator.php?mode=mkgp"><img src="images/mkgp.png"></a></br><br>';
       echo '<a href="cardemulator.php?mode=fzero"><img src="images/FZ.png"></a></br>';
    }
}

if ($mode == 'idas'){
   echo '<b>Emulator Mode: Initial D ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/idas/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/idas/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=idas&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="idcards.php?name='.$file.'&amp;mode=idas"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="idas"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}

if ($mode == 'id2'){
   echo '<b>Emulator Mode: Initial D ver.2 ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/id2/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/id2/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=id2&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="idcards.php?name='.$file.'&amp;mode=id2"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="id2"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}

if ($mode == 'id3'){
   echo '<b>Emulator Mode: Initial D ver.3 ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/id3/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/id3/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=id3&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="idcards.php?name='.$file.'&amp;mode=id3"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="id3"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}

if ($mode == 'wmmt'){
   echo '<b>Emulator Mode: Wangan Midnight</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/wmmt/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=wmmt&launchmode=manual"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=wmmt"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="wmmt"><input type="hidden" name="launchmode" value="manual"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'mkgp'){
   echo '<b>Emulator Mode: Mario Kart GP/GP2</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/mkgp/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=mkgp&launchmode=manual"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=mkgp"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="mkgp"><input type="hidden" name="launchmode" value="manual"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'fzero'){
   echo '<b>Emulator Mode: F-Zero AX ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/fzero/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/fzero/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=fzero&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="fzcards.php?name='.$file.'&amp;mode=fzero"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="fzero"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}
}
if ($ui_mode === 'modern' && $mode != 'main') {
    echo '</div></div></div>'; // Close flex container, card-body, card
}
if ($ui_mode === 'modern') {
    echo '</div></div>'; // Close main-content, container
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}</script>';
    echo '</body></html>';
} else {
    echo '</p></center></section></body></html>';
}
?>
