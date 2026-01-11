<?php

// Load UI mode helper
include_once 'ui_mode.php';
$ui_mode = get_ui_mode();

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Network Configuration">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';

// Load CSS based on UI mode
load_ui_styles();

// Only include old menu for classic UI
if ($ui_mode === 'classic') {
    include 'menu_include.php';
}

$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');
?>

<!DOCTYPE html>
<html>
<body>

<?php

// Modern UI wrapper
if ($ui_mode === 'modern') {
    echo '<button class="burger-menu" id="burgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu"><span></span><span></span><span></span></button>';
    echo '<div class="sidebar-nav" id="sidebarNav">';
    echo '<nav>';
    echo '<a href="gamelist.php?display=all" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎮</span><span class="sidebar-nav-label">Games</span></a>';
    echo '<a href="dimms.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">💾</span><span class="sidebar-nav-label">NetDIMMs</span></a>';
    echo '<a href="setup.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">⚙️</span><span class="sidebar-nav-label">Setup</span></a>';
    echo '<a href="options.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🏛️</span><span class="sidebar-nav-label">Options</span></a>';
    echo '<a href="network.php" class="sidebar-nav-item active">';
    echo '<span class="sidebar-nav-icon">🌐</span><span class="sidebar-nav-label">Network</span></a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    echo '<div class="main-content">';
    echo '<div class="container" style="padding: 20px;">';
    echo '<h1 class="text-3xl" style="margin-bottom: 24px;">🌐 Network Configuration</h1>';
    
    // Action buttons
    echo '<div class="flex" style="gap: 12px; margin-bottom: 32px;">';
    echo '<a href="scanning.php" class="btn btn-primary">📶 WiFi Setup</a>';
    echo '<a href="wired.php" class="btn btn-secondary">🔌 Wired Setup</a>';
    echo '<a href="interfaceseditor.php" class="btn btn-secondary">⚙️ Advanced Editor</a>';
    echo '</div>';
} else {
    echo '<section><center>';
    echo '<h1><a href="setup.php">Network Setup</a></h1><br>';
    echo '<form action="scanning.php"><button type="submit" class="dropbtn" value="Cancel">Wifi Setup</button> <a href="wired.php" style="font-weight:normal" class="dropbtn">Wired Setup</a></form>';
    echo '<br><br>';
}

// Network information retrieval
$wiredip = `ip -o -f inet addr show | awk '/eth0/ {print $4}'`;
$wirelessip = `ip -o -f inet addr show | awk '/wlan0/ {print $4}'`;
$wiredstatus =  `ip -o -f inet addr show | awk '/eth0/ {print $9}'`;
$wirelessstatus = `ip -o -f inet addr show | awk '/wlan0/ {print $9}'`;
$ssid = `iwgetid -r`;
if ($wiredstatus == "dynamic\n"){$wiredtype = "DHCP";}else{$wiredtype = "Static";}
if ($wirelessstatus == "dynamic\n"){$wirelesstype = "DHCP";}else{$wirelesstype = "Static";}

if ($ui_mode === 'modern') {
    // Modern UI - Network Status Cards
    echo '<div class="grid grid-cols-2" style="margin-bottom: 32px;">';
    
    // Wireless Status
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">📶 Wireless Network</h3></div>';
    echo '<div class="card-body">';
    echo '<p><strong>IP Address:</strong> '.($wirelessip ?: 'Not connected').'</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">'.$wirelesstype.'</span></p>';
    if ($wifimode == 'hotspot') {
        echo '<p><strong>Mode:</strong> <span class="badge badge-primary">HotSpot</span></p>';
        echo '<p><strong>SSID:</strong> WiPi-Netbooter</p>';
    } else {
        echo '<p><strong>Mode:</strong> <span class="badge badge-success">Home WiFi</span></p>';
        echo '<p><strong>SSID:</strong> '.trim($ssid).'</p>';
    }
    echo '</div></div>';
    
    // Wired Status
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">🔌 Wired Network</h3></div>';
    echo '<div class="card-body">';
    echo '<p><strong>IP Address:</strong> '.($wiredip ?: 'Not connected').'</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">'.$wiredtype.'</span></p>';
    echo '<p><strong>Default:</strong> 10.0.0.1</p>';
    echo '</div></div>';
    
    echo '</div>';
    
    // Information Alert
    echo '<div class="alert alert-info" style="margin-bottom: 32px;">';
    echo '<p><strong>ℹ️ Access the Pi:</strong></p>';
    echo '<p>From iOS/Linux/Windows: <code>http://netbooter.local</code></p>';
    echo '<p>From Android: Use Fing or similar network scanner</p>';
    echo '<p><strong>🚨 Recovery:</strong> Create <code>reset.txt</code> on boot partition to restore hotspot mode</p>';
    echo '</div>';
    
    // Configuration Modes
    echo '<h2 style="margin-bottom: 16px;">Network Configuration Modes</h2>';
    echo '<div class="grid grid-cols-2" style="margin-bottom: 32px;">';
    
    // Hotspot Direct
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">Hotspot Direct (Default)</h3></div>';
    echo '<div class="card-body">';
    echo '<img src="img/hsdirect.png" style="width: 100%; margin-bottom: 16px; border-radius: 8px;" alt="Hotspot Direct">';
    echo '<p>Pi broadcasts WiFi and connects directly to NetDIMM via Ethernet</p>';
    echo '<p><strong>Pi Wireless:</strong> 192.168.42.1</p>';
    echo '<p><strong>Pi Wired:</strong> 10.0.0.1</p>';
    echo '<p><strong>NetDIMM:</strong> 10.0.0.2</p>';
    echo '<p><span class="badge badge-success">✓ No router needed</span></p>';
    echo '</div></div>';
    
    // Home WiFi Direct
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">Home WiFi Direct</h3></div>';
    echo '<div class="card-body">';
    echo '<img src="img/homedirect.png" style="width: 100%; margin-bottom: 16px; border-radius: 8px;" alt="Home WiFi Direct">';
    echo '<p>Pi connects to home WiFi, direct Ethernet to NetDIMM</p>';
    echo '<p><strong>Pi Wireless:</strong> DHCP/Static on home network</p>';
    echo '<p><strong>Pi Wired:</strong> 10.0.0.1</p>';
    echo '<p><strong>NetDIMM:</strong> 10.0.0.2</p>';
    echo '<p><span class="badge badge-primary">✓ Convenient access</span></p>';
    echo '</div></div>';
    
    // Hotspot Router
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">Hotspot Router Mode</h3></div>';
    echo '<div class="card-body">';
    echo '<img src="img/hsrouter.png" style="width: 100%; margin-bottom: 16px; border-radius: 8px;" alt="Hotspot Router">';
    echo '<p>Pi broadcasts WiFi, both Pi and NetDIMM connect to home router via Ethernet</p>';
    echo '<p><strong>Pi Wireless:</strong> 192.168.42.1 (hotspot)</p>';
    echo '<p><strong>Pi Wired:</strong> DHCP/Static on home network</p>';
    echo '<p><strong>NetDIMM:</strong> DHCP/Static on home network</p>';
    echo '<p><span class="badge badge-primary">✓ Dual access</span></p>';
    echo '</div></div>';
    
    // Home WiFi Router
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">Home WiFi Router Mode</h3></div>';
    echo '<div class="card-body">';
    echo '<img src="img/homerouter.png" style="width: 100%; margin-bottom: 16px; border-radius: 8px;" alt="Home WiFi Router">';
    echo '<p>Pi connects via WiFi, NetDIMM connects via router (both on home network)</p>';
    echo '<p><strong>Pi Wireless:</strong> DHCP/Static on home network</p>';
    echo '<p><strong>NetDIMM:</strong> DHCP/Static on home network (via router)</p>';
    echo '<p><span class="badge badge-success">✓ Full home integration</span></p>';
    echo '</div></div>';
    
    echo '</div>';
    
    echo '</div></div>'; // Close main-content and container
    
    // Add sidebar toggle script
    echo '<script>';
    echo 'function toggleSidebar() {';
    echo '  const sidebar = document.getElementById("sidebarNav");';
    echo '  const overlay = document.getElementById("sidebarOverlay");';
    echo '  const burger = document.getElementById("burgerBtn");';
    echo '  sidebar.classList.toggle("open");';
    echo '  overlay.classList.toggle("show");';
    echo '  burger.classList.toggle("open");';
    echo '}';
    echo '</script>';
} else {
    // Classic UI
    echo 'Wireless IP: <b>'.$wirelessip.' ('.$wirelesstype.')</b><br>';
    echo 'Wired IP: <b>'.$wiredip.' ('.$wiredtype.')</b><br><br>';
    if ($wifimode == 'hotspot'){
        echo 'Current Wifi Mode: <b>HotSpot</b><br><br>';}
    else {echo 'Current Wifi Mode: <b>Home WiFi</b><br>Current SSID: <b>'.$ssid.'</b><br><br>';}
    echo 'The WiPi Netbooter supports multiple network configurations detailed below<br><br>';
    echo 'You can use the Wifi and Wired Setup options to customise the setup for your preferred configuration<br><br>';
    echo 'When the Pi is joined to your home Wifi network you can browse to it from IOS, Linux and Windows 10 devices using the URL <b>http://netbooter.local</b><br><br>';
    echo 'If you are running Android you need to use a network scanner app to locate it, Fing is recommended<br><br>';
    echo '<b>NOTE:</b> If at any time the Pi becomes unavailable due to a network change it can be reset to the default hotspot mode by creating a file called <b>reset.txt</b> in the boot partition of the SD card and the Pi booted up<br><br>';
    echo 'For advanced setups you can directly edit the network interfaces file <a href="interfaceseditor.php">here</a><br><br>';
    echo '<b>Default Configuration</b><br><br>';
    echo 'The default configuration is <b>Hotspot Direct</b><br><br>';
    echo '<img src="img/hsdirect.png" id="largeimg"><br><br>';
    echo 'The Pi broadcasts a wireless network and the wired interface is set to use an IP address of <b>10.0.0.1</b><br><br>';
    echo 'The Netdimm connected to the Pi should be configured to run on <b>10.0.0.2</b><br><br>';
    echo 'The Pi wireless address is <b>192.168.42.1</b><br><br>';
    echo 'Connection is direct to the Pi and to the Netdimm so there is no need for a router or switch<br><br>';
    echo '<b>Home Wifi Direct</b><br><br>';
    echo '<img src="img/homedirect.png" id="largeimg"><br><br>';
    echo 'In this mode the Pi is connected via Wifi to your home router and the Pi is directly connected to the Netdimm using <b>10.0.0.1</b><br><br>';
    echo 'This mode is useful if you don\'t want to change Wifi networks each time you use the Netbooter, the downside is you need to locate your Pi on your home network<br><br>';
    echo 'If you know your home IP address range you can set a static IP address on the Pi<br><br>';
    echo '<b>Hotspot Router Mode</b><br><br>';
    echo '<img src="img/hsrouter.png" id="largeimg"><br><br>';
    echo 'The Pi broadcasts a wireless network and is connected via the wired interface to your home router<br><br>';
    echo 'The Netdimm also needs to be connected to your home network so the Pi can netboot games<br><br>';
    echo 'You can use a static or DHCP address for both the Pi and the Netdimm, using static IP addresses is highly recommended<br><br>';
    echo 'One advantage with this setup is that the Pi can be reached via the home router network as well as the hotspot<br><br>';
    echo '<b>Home Wifi Router Mode</b><br><br>';
    echo '<img src="img/homerouter.png" id="largeimg"><br><br>';
    echo 'In this mode the Pi is connected via Wifi to your home router and the Netdimm is connected via a network cable<br><br>';
    echo 'This mode is useful if you don\'t want to change Wifi networks each time you use the Netbooter, the downside is you need to locate your Pi on your home network<br><br>';
    echo 'If you know your home IP address range you can set a static IP address on the Pi<br><br>';
    echo 'A static address is also recommended for the Netdimm<br><br>';
}
?>

</body>
</html>
