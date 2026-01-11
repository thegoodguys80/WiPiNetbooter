<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu_include.php';
}

$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');

// SECURITY: Initialize error variable
$error = '';

if(isset($_POST["submit"]))
{
    // SECURITY: Validate all network inputs
    $ip = $_POST["ip"] ?? '';
    $sm = $_POST["sm"] ?? '';
    $gw = $_POST["gw"] ?? '';

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Validate subnet mask
        if (!filter_var($sm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Subnet mask is invalid</b></font><br>';
        }
        // Validate gateway (if provided and not 'none')
        elseif ($gw != '' && $gw != 'none' && !filter_var($gw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Gateway is invalid</b></font><br>';
        }
        else {
            if ($gw == '') {
                $gw = 'none';
            }
            // SECURITY: Use escapeshellarg for each parameter
            $command = 'sudo python /sbin/piforce/setstatic.py wired ' . 
                       escapeshellarg($wifimode) . ' ' . 
                       escapeshellarg($ip) . ' ' . 
                       escapeshellarg($sm) . ' ' . 
                       escapeshellarg($gw);
            shell_exec($command);
            $error = '<font color="green"><b>Wired Settings Updated</b></font>';
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

if(isset($_POST["dhcp"]))
{
    if($error == '')
    {
        // SECURITY: Use escapeshellarg for parameter
        $command = 'sudo python /sbin/piforce/setdhcp.py wired ' . escapeshellarg($wifimode);
        shell_exec($command);
        $error = '<font color="green"><b>Wired Settings Updated</b></font>';
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
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Wired Setup</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    // Sidebar
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
    
    echo '<h1>🔌 Wired Network Setup</h1>';
    
    if ($error) {
        echo '<div class="alert alert-' . (strpos($error, 'green') !== false ? 'success' : 'error') . '">' . strip_tags($error) . '</div>';
    }
    
    // Status cards
    echo '<div class="grid grid-cols-2" style="margin-bottom: 24px;">';
    echo '<div class="card"><div class="card-header"><h3 class="card-title">📶 Wireless</h3></div><div class="card-body">';
    echo '<p><strong>IP:</strong> ' . htmlspecialchars(trim($wirelessip), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">' . htmlspecialchars($wirelesstype, ENT_QUOTES, 'UTF-8') . '</span></p>';
    if ($wifimode == 'hotspot') {
        echo '<p><strong>Mode:</strong> HotSpot</p>';
    } else {
        echo '<p><strong>SSID:</strong> ' . htmlspecialchars(trim($ssid), ENT_QUOTES, 'UTF-8') . '</p>';
    }
    echo '</div></div>';
    echo '<div class="card"><div class="card-header"><h3 class="card-title">🔌 Wired</h3></div><div class="card-body">';
    echo '<p><strong>IP:</strong> ' . htmlspecialchars(trim($wiredip), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">' . htmlspecialchars($wiredtype, ENT_QUOTES, 'UTF-8') . '</span></p>';
    echo '</div></div></div>';
    
    // Configuration forms
    echo '<div class="card" style="margin-bottom: 24px;"><div class="card-header"><h3 class="card-title">Static IP Configuration</h3></div><div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Configure a static IP address for the wired interface</p>';
    echo '<div class="alert alert-info" style="margin-bottom: 16px;"><strong>Note:</strong> Only provide a Gateway if connecting to your router</div>';
    echo '<form method="post"><div class="form-group"><label>IP Address</label><input type="text" name="ip" class="form-control" placeholder="192.168.1.100"></div>';
    echo '<div class="form-group"><label>Subnet Mask</label><input type="text" name="sm" class="form-control" placeholder="255.255.255.0"></div>';
    echo '<div class="form-group"><label>Gateway (Optional)</label><input type="text" name="gw" class="form-control" placeholder="192.168.1.1"></div>';
    echo '<button type="submit" name="submit" class="btn btn-primary">✓ Apply and Reboot</button></form></div></div>';
    
    echo '<div class="card"><div class="card-header"><h3 class="card-title">DHCP Configuration</h3></div><div class="card-body">';
    echo '<p style="margin-bottom: 16px;">Switch wired interface to DHCP (automatic IP assignment)</p>';
    echo '<div class="alert alert-warning"><strong>Warning:</strong> Not recommended to run DHCP on both wireless and wired simultaneously</div>';
    echo '<form method="post"><button type="submit" name="dhcp" class="btn btn-warning">Switch to DHCP</button></form></div></div>';
    
    echo '</div></div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");s.classList.toggle("open");o.classList.toggle("show");b.classList.toggle("open");}</script>';
    echo '</body></html>';
} else {
    // Classic UI
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center><h1><a href="network.php">Wired Setup</a></h1>';
    echo 'Wireless IP: <b>' . htmlspecialchars(trim($wirelessip), ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($wirelesstype, ENT_QUOTES, 'UTF-8') . ')</b><br>';
    echo 'Wired IP: <b>' . htmlspecialchars(trim($wiredip), ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($wiredtype, ENT_QUOTES, 'UTF-8') . ')</b><br><br>';
    if ($wifimode == 'hotspot'){
        echo 'Current Wifi Mode: <b>HotSpot</b><br><br>';}
    else {echo 'Current Wifi Mode: <b>Home WiFi</b><br>Current SSID: <b>' . htmlspecialchars(trim($ssid), ENT_QUOTES, 'UTF-8') . '</b><br><br>';}
    if ($wifimode == 'hotspot'){
        echo 'The Pi is currently set up in HotSpot mode broadcasting its own WiFi network<br><br>';}
    else {
        echo 'The Pi is currently set up in Home WiFi mode<br><br>';}
    echo 'Use the form below to update the static IP address on the wired interface<br><br>';
    echo '<b>NOTE:</b> Do not provide a Gateway unless you are connecting the wired interface to your router<br><br>';
    echo 'The Pi will reboot and update the settings.<br><br>';
    echo '<div class="box2"><br><form method="post" id="form1">';
    echo '<b><label for="ip">IP Address: </label><input type="text" size="10" id="ip" name="ip"><br><br>';
    echo '<label for="sm">Subnet Mask: </label><input type="text" size="10" id="sm" name="sm"><br><br>';
    echo '<label for="gw">Gateway: </label><input type="text" size="10" id="gw" name="gw"></b><br><br>';
    echo '<input type="submit" name="submit" class="dropbtn" value="Apply and Reboot"><br><br></div><br><br>';
    echo 'If you would like to change the wired interface to DHCP use the button below<br><br>';
    echo 'It is not recommended to run DHCP on wireless and wired at the same time as it may cause connection issues<br><br>';
    echo '<input type="submit" class="dropbtn" name="dhcp" value="Wired DHCP"></form>';
    echo '<br><br>' . $error . '</p></center></body></html>';
}
?>
