<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu.php';
}

include 'wifilist.php';
$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');

if(isset($_POST["submit"]))
{

// SECURITY: Validate all network configuration inputs
$ip = $_POST["ip"] ?? '';
$sm = $_POST["sm"] ?? '';
$gw = $_POST["gw"] ?? '';

if($ip != ''){
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Validate subnet mask
        if (!filter_var($sm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Subnet mask is invalid</b></font><br>';
        }
        // Validate gateway (if provided)
        elseif ($gw != '' && !filter_var($gw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Gateway is invalid</b></font><br>';
        }
        else {
            // SECURITY: Use escapeshellarg for each parameter
            $command = 'sudo python /sbin/piforce/setstatic.py wireless ' . 
                       escapeshellarg($wifimode) . ' ' . 
                       escapeshellarg($ip) . ' ' . 
                       escapeshellarg($sm) . ' ' . 
                       escapeshellarg($gw);
            shell_exec($command);
        }
    }
    else{
        $error .= '<font color="red"><b>IP Address is invalid</b></font><br>';
    }
}

 // SECURITY: Validate SSID and PSK inputs
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'] ?? '';
 }
 else
 {
  $ssid = $_POST['manssid'] ?? '';
 }
 
 // Validate SSID (max 32 characters, alphanumeric and common symbols)
 if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
     $error .= '<font color="red"><b>SSID is invalid (max 32 chars, alphanumeric only)</b></font><br>';
 }
 
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else if(strlen($_POST["psk"]) > 63)
 {
  $error .= '<font color="red"><b> Password must be 63 characters or less</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  // SECURITY: Use escapeshellarg for parameters
  $wificommand = 'sudo python /sbin/piforce/wificopy.py ' . 
                 escapeshellarg($ssid) . ' ' . 
                 escapeshellarg($psk);
  shell_exec($wificommand);
  $error = '<font color="green"><b>Wifi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = 'sudo python /sbin/piforce/reboot.py';
  shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["hotspotsubmit"]))
{
 // SECURITY: Validate SSID and PSK inputs
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'] ?? '';
 }
 else
 {
  $ssid = $_POST['manssid'] ?? '';
 }
 
 // Validate SSID
 if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
     $error .= '<font color="red"><b>SSID is invalid (max 32 chars, alphanumeric only)</b></font><br>';
 }
 
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else if(strlen($_POST["psk"]) > 63)
 {
  $error .= '<font color="red"><b> Password must be 63 characters or less</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  // SECURITY: Use escapeshellarg for parameters
  $command = 'sudo python /sbin/piforce/hotspotupdate.py ' . 
             escapeshellarg($ssid) . ' ' . 
             escapeshellarg($psk);
  shell_exec($command);
  $error = '<font color="green"><b>HotSpot Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = 'sudo python /sbin/piforce/reboot.py';
  shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["homesubmit"]))
{
 // SECURITY: Validate SSID and PSK inputs
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'] ?? '';
 }
 else
 {
  $ssid = $_POST['manssid'] ?? '';
 }
 
 // Validate SSID
 if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
     $error .= '<font color="red"><b>SSID is invalid (max 32 chars, alphanumeric only)</b></font><br>';
 }
 
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else if(strlen($_POST["psk"]) > 63)
 {
  $error .= '<font color="red"><b> Password must be 63 characters or less</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  // SECURITY: Use escapeshellarg for parameters
  $command = 'sudo python /sbin/piforce/homeupdate.py ' . 
             escapeshellarg($ssid) . ' ' . 
             escapeshellarg($psk);
  shell_exec($command);
  $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = 'sudo python /sbin/piforce/reboot.py';
  shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["static"]))
{
    // SECURITY: Validate network configuration inputs
    $ip = $_POST["ip"] ?? '';
    $sm = $_POST["sm"] ?? '';
    $gw = $_POST["gw"] ?? '';

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Validate subnet mask
        if (!filter_var($sm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Subnet mask is invalid</b></font><br>';
        }
        // Validate gateway (if provided)
        elseif ($gw != '' && !filter_var($gw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Gateway is invalid</b></font><br>';
        }
        elseif($error == '')
        {
            // SECURITY: Use escapeshellarg for each parameter
            $command = 'sudo python /sbin/piforce/setstatic.py wireless ' . 
                       escapeshellarg($wifimode) . ' ' . 
                       escapeshellarg($ip) . ' ' . 
                       escapeshellarg($sm) . ' ' . 
                       escapeshellarg($gw);
            shell_exec($command);
            $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
            $rebootcommand = 'sudo python /sbin/piforce/reboot.py';
            shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
            $ssid = '';
            $psk = '';
        }
    }
    else{
        $error .= '<font color="red"><b>IP Address is invalid</b></font><br>';
    }
}

if(isset($_POST["wifidhcp"]))
{
    if($error == '')
    {
        // SECURITY: No user input, static command only
        $command = 'sudo python /sbin/piforce/setdhcp.py wireless &';
        shell_exec($command);
        $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
        $rebootcommand = 'sudo python /sbin/piforce/reboot.py';
        shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
    }
}

if(isset($_POST["hotspotrestore"]))
{
    if($error == '')
    {
        // SECURITY: No user input, static command only
        $command = 'sudo python /sbin/piforce/hotspotwifi.py &';
        shell_exec($command);
        $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
        $rebootcommand = 'sudo python /sbin/piforce/reboot.py';
        shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
    }
}


$wiredip = `ip -o -f inet addr show | awk '/eth0/ {print $4}'`;
$wirelessip = `ip -o -f inet addr show | awk '/wlan0/ {print $4}'`;
$wiredstatus =  `ip -o -f inet addr show | awk '/eth0/ {print $9}'`;
$wirelessstatus = `ip -o -f inet addr show | awk '/wlan0/ {print $9}'`;
$ssid = `iwgetid -r`;
if ($wiredstatus == "dynamic\n"){$wiredtype = "DHCP";}else{$wiredtype = "Static";}
if ($wirelessstatus == "dynamic\n"){$wirelesstype = "DHCP";}else{$wirelesstype = "Static";}

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - WiFi Setup</title>';
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
    echo '<h1>📶 WiFi Setup</h1>';
    
    if ($error) {
        $alertType = (strpos($error, 'green') !== false) ? 'success' : 'error';
        echo '<div class="alert alert-' . $alertType . '">' . strip_tags($error) . '</div>';
    }
    
    echo '<div class="grid grid-cols-2" style="margin-bottom: 24px;">';
    echo '<div class="card"><div class="card-header"><h3 class="card-title">📶 Wireless</h3></div><div class="card-body">';
    echo '<p><strong>IP:</strong> ' . htmlspecialchars(trim($wirelessip), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">' . htmlspecialchars($wirelesstype, ENT_QUOTES, 'UTF-8') . '</span></p>';
    if ($wifimode == 'hotspot') {
        echo '<p><strong>Mode:</strong> <span class="badge badge-primary">HotSpot</span></p>';
    } else {
        echo '<p><strong>Mode:</strong> <span class="badge badge-success">Home WiFi</span></p>';
        echo '<p><strong>SSID:</strong> ' . htmlspecialchars(trim($ssid), ENT_QUOTES, 'UTF-8') . '</p>';
    }
    echo '</div></div>';
    echo '<div class="card"><div class="card-header"><h3 class="card-title">🔌 Wired</h3></div><div class="card-body">';
    echo '<p><strong>IP:</strong> ' . htmlspecialchars(trim($wiredip), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">' . htmlspecialchars($wiredtype, ENT_QUOTES, 'UTF-8') . '</span></p>';
    echo '</div></div></div>';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center><h1><a href="network.php">WiFi Setup</a></h1>';
    echo 'Wireless IP: <b>' . htmlspecialchars(trim($wirelessip), ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($wirelesstype, ENT_QUOTES, 'UTF-8') . ')</b><br>';
    echo 'Wired IP: <b>' . htmlspecialchars(trim($wiredip), ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($wiredtype, ENT_QUOTES, 'UTF-8') . ')</b><br><br>';
    if ($wifimode == 'hotspot'){
        echo 'Current Wifi Mode: <b>HotSpot</b><br><br>';}
    else {echo 'Current Wifi Mode: <b>Home WiFi</b><br>Current SSID: <b>' . htmlspecialchars(trim($ssid), ENT_QUOTES, 'UTF-8') . '</b><br><br>';}
}
if ($ui_mode === 'modern') {
    if ($wifimode == 'hotspot') {
        echo '<div class="card" style="margin-bottom: 24px;"><div class="card-header"><h3 class="card-title">Connect to Home WiFi</h3></div><div class="card-body">';
        echo '<p style="margin-bottom: 16px;">Switch from HotSpot mode to your home WiFi network</p>';
        echo '<form method="post"><div class="form-group"><label>WiFi SSID (Select)</label><select name="ssid" class="form-control">';
        for ($i = 1; $i <= $ssids; $i++) {
            $name = ${'name'.$i};
            echo '<option value="'.$name.'">'.$name.'</option>';}
        echo '</select></div><div class="form-group"><label>WiFi SSID (Manual)</label><input type="text" name="manssid" class="form-control" placeholder="Or type SSID manually"></div>';
        echo '<div class="form-group"><label>Password</label><input type="password" name="psk" class="form-control" required></div>';
        echo '<p style="margin: 16px 0; font-weight: bold;">Optional Static IP</p>';
        echo '<div class="form-group"><label>IP Address</label><input type="text" name="ip" class="form-control" placeholder="Leave blank for DHCP"></div>';
        echo '<div class="form-group"><label>Subnet Mask</label><input type="text" name="sm" class="form-control"></div>';
        echo '<div class="form-group"><label>Gateway</label><input type="text" name="gw" class="form-control"></div>';
        echo '<button type="submit" name="submit" class="btn btn-primary">✓ Apply and Reboot</button></form></div></div>';
        
        echo '<div class="card"><div class="card-header"><h3 class="card-title">Update HotSpot Settings</h3></div><div class="card-body">';
        echo '<p style="margin-bottom: 16px;">Change your HotSpot SSID and password</p>';
        echo '<form method="post"><div class="form-group"><label>HotSpot SSID</label><input type="text" name="ssid" class="form-control"></div>';
        echo '<div class="form-group"><label>New Password</label><input type="password" name="psk" class="form-control"></div>';
        echo '<button type="submit" name="hotspotsubmit" class="btn btn-primary">Apply and Reboot</button></form></div></div>';
    } else {
        echo '<div class="card" style="margin-bottom: 24px;"><div class="card-header"><h3 class="card-title">Update Home WiFi</h3></div><div class="card-body">';
        echo '<p style="margin-bottom: 16px;">Change your home WiFi connection</p>';
        echo '<form method="post"><div class="form-group"><label>WiFi SSID (Select)</label><select name="ssid" class="form-control">';
        for ($i = 1; $i <= $ssids; $i++) {
            $name = ${'name'.$i};
            echo '<option value="'.$name.'">'.$name.'</option>';}
        echo '</select></div><div class="form-group"><label>WiFi SSID (Manual)</label><input type="text" name="manssid" class="form-control" placeholder="Or type SSID manually"></div>';
        echo '<div class="form-group"><label>Password</label><input type="password" name="psk" class="form-control" required></div>';
        echo '<button type="submit" name="homesubmit" class="btn btn-primary">✓ Apply and Reboot</button></form></div></div>';
        
        echo '<div class="card" style="margin-bottom: 24px;"><div class="card-header"><h3 class="card-title">Static IP Configuration</h3></div><div class="card-body">';
        echo '<form method="post"><div class="form-group"><label>IP Address</label><input type="text" name="ip" class="form-control"></div>';
        echo '<div class="form-group"><label>Subnet Mask</label><input type="text" name="sm" class="form-control"></div>';
        echo '<div class="form-group"><label>Gateway</label><input type="text" name="gw" class="form-control"></div>';
        echo '<button type="submit" name="static" class="btn btn-primary">Apply and Reboot</button></form></div></div>';
        
        echo '<div class="card"><div class="card-header"><h3 class="card-title">Network Options</h3></div><div class="card-body"><form method="post">';
        if ($wirelesstype == "Static") {
            echo '<p style="margin-bottom: 16px;">Return to automatic IP assignment</p>';
            echo '<button type="submit" name="wifidhcp" class="btn btn-secondary" style="margin-right: 8px;">Switch to DHCP</button>';
        }
        echo '<button type="submit" name="hotspotrestore" class="btn btn-warning">Restore HotSpot Mode</button></form></div></div>';
    }
    echo '</div></div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}</script>';
    echo '</body></html>';
} else {
    if ($wifimode == 'hotspot'){
    echo 'The Pi is currently set up in HotSpot mode broadcasting its own WiFi network<br><br>';
    echo 'Use the form below to enter your home network details then press the Apply button to disable HotSpot Mode<br><br>';
    echo 'Either choose your Wifi SSID from the drop down, or manually type it into the box<br><br>';
    echo 'The Pi will reboot and connect to your home WiFi network<br><br>You also can provide a static IP address for your home network or leave blank for DHCP<br><br>Hotspot mode can be restored later<br><br>';
    
    echo '<form method="post" id="form1">';
    echo '<div class="box2"><br>';
    echo '<b><label for="ssid">WiFi SSID: </label>';
    echo '<select name="ssid">';
      for ($i = 1; $i <= $ssids; $i++) {
      $name = ${'name'.$i};
      echo '<option value="'.$name.'">'.$name.'</option>';}
    echo '</select><br><br>';
    echo '<label for="manssid">WiFi SSID: </label>';
    echo '<input type="text" size="10" id="manssid" name="manssid"><br><br>';
    echo '<label for="psk">Password: </label>';
    echo '<input type="text" size="10" id="psk" name="psk"><br><br>';
    echo '===OPTIONAL===<br><br>';
    echo '<label for="psk">IP Address: </label>';
    echo '<input type="text" size="10" id="ip" name="ip"><br><br>';
    echo '<label for="psk">Subnet Mask: </label>';
    echo '<input type="text" size="10" id="sm" name="sm"><br><br>';
    echo '<label for="psk">Gateway: </label>';
    echo '<input type="text" size="10" id="gw" name="gw"></b><br><br>';
    echo '<input type="submit" name="submit" class="dropbtn" value="Apply and Reboot">';
    echo '</form></div><br><br>';
    echo 'If you would like to change the default HotSpot settings use the form below<br><br>';
    echo '<form action="wifi.php" method="post" id="form2">';
    echo '<div class="box2"><br>';
    echo '<b><label for="ssid">HotSpot SSID: </label>';
    echo '<input type="text" size="10" id="ssid" name="ssid"><br><br>';
    echo '<label for="psk">New Password: </label>';
    echo '<input type="text" size="10" id="psk" name="psk"></b><br><br>';
    echo '<input type="submit" class="dropbtn" name="hotspotsubmit" value="Apply and Reboot"></div></form>';
    } else {
    echo 'The Pi is currently set up in Home WiFi mode<br><br>';
    echo 'To change your home network details use the form below and press Apply<br><br>';
    echo 'Either choose your Wifi SSID from the drop down or manually type it into the box<br><br>';
    echo 'The Pi will reboot and connect to your new home WiFi network<br><br>';
    echo '<div class="box2"><br>';
    echo '<form action="wifi.php" method="post" id="form1">';
    echo '<b><label for="ssid">WiFi SSID: </label>';
    echo '<select name="ssid">';
      for ($i = 1; $i <= $ssids; $i++) {
      $name = ${'name'.$i};
      echo '<option value="'.$name.'">'.$name.'</option>';}
    echo '</select><br><br>';
    echo '<label for="manssid">WiFi SSID: </label>';
    echo '<input type="text" size="10" id="manssid" name="manssid"><br><br>';
    echo '<label for="psk">Password: </label>';
    echo '<input type="text" size="10" id="psk" name="psk"></b><br><br>';
    echo '<input type="submit" class="dropbtn" name="homesubmit" value="Apply and Reboot"><br><br></div>';
    echo '<br><br>';
    
    echo 'Use the form below to set or update the static IP address on the wireless interface<br><br>';
    echo 'The Pi will reboot and update the settings<br><br>';
    echo '<div class="box2"><br>';
    echo '<form method="post" id="form1">';
    echo '<b><label for="ip">IP Address: </label>';
    echo '<input type="text" size="10" id="ip" name="ip"><br><br>';
    echo '<label for="sm">Subnet Mask: </label>';
    echo '<input type="text" size="10" id="sm" name="sm"><br><br>';
    echo '<label for="gw">Gateway: </label>';
    echo '<input type="text" size="10" id="gw" name="gw"></b><br><br>';
    echo '<input type="submit" name="static" class="dropbtn" value="Apply and Reboot"><br><br></div>';
    echo '<br><br>';
    
    if ($wirelesstype == "Static"){
    echo 'To return to DHCP mode use the button below<br><br>';
    echo '<input type="submit" class="dropbtn" name="wifidhcp" value="Wireless DHCP">';
    echo '<br><br>';
    }
    
    echo 'To return to hotspot mode use the button below<br><br>';
    echo '<input type="submit" class="dropbtn" name="hotspotrestore" value="Hotspot Mode">';
    echo '</form>';
    echo '<br><br>' . $error . '</p></center></body></html>';
    }
}
?>
