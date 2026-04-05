<?php

include_once 'ui_mode.php';

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Network Configuration">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';

load_ui_styles();

$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');
?>

<!DOCTYPE html>
<html>
<body>

<?php

echo modern_sliding_sidebar_nav('network');
echo '<div class="container" style="padding: 20px;">';
echo '<h1 class="text-3xl" style="margin-bottom: 24px;">'.arcade_icon('globe').' Network Configuration</h1>';

// Action buttons
echo '<div class="flex" style="gap: 12px; margin-bottom: 32px;">';
echo '<a href="scanning.php" class="btn btn-primary">'.arcade_icon('network').' WiFi Setup</a>';
echo '<a href="wired.php" class="btn btn-secondary">'.arcade_icon('plug').' Wired Setup</a>';
echo '<a href="interfaceseditor.php" class="btn btn-secondary">'.arcade_icon('setup').' Advanced Editor</a>';
echo '</div>';

// Network information retrieval
$wiredip = `ip -o -f inet addr show | awk '/eth0/ {print $4}'`;
$wirelessip = `ip -o -f inet addr show | awk '/wlan0/ {print $4}'`;
$wiredstatus =  `ip -o -f inet addr show | awk '/eth0/ {print $9}'`;
$wirelessstatus = `ip -o -f inet addr show | awk '/wlan0/ {print $9}'`;
$ssid = `iwgetid -r`;
if ($wiredstatus == "dynamic\n"){$wiredtype = "DHCP";}else{$wiredtype = "Static";}
if ($wirelessstatus == "dynamic\n"){$wirelesstype = "DHCP";}else{$wirelesstype = "Static";}

    // Network Status Cards
    echo '<div class="grid grid-cols-2" style="margin-bottom: 32px;">';
    
    // Wireless Status
    echo '<div class="card">';
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('network').' Wireless Network</h3></div>';
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
    echo '<div class="card-header"><h3 class="card-title">'.arcade_icon('plug').' Wired Network</h3></div>';
    echo '<div class="card-body">';
    echo '<p><strong>IP Address:</strong> '.($wiredip ?: 'Not connected').'</p>';
    echo '<p><strong>Type:</strong> <span class="badge badge-primary">'.$wiredtype.'</span></p>';
    echo '<p><strong>Default:</strong> 10.0.0.1</p>';
    echo '</div></div>';
    
    echo '</div>';
    
    // Information Alert
    echo '<div class="alert alert-info" style="margin-bottom: 32px;">';
    echo '<p><strong>'.arcade_icon('help').' Access the Pi:</strong></p>';
    echo '<p>From iOS/Linux/Windows: <code>http://netbooter.local</code></p>';
    echo '<p>From Android: Use Fing or similar network scanner</p>';
    echo '<p><strong>'.arcade_icon('warning').' Recovery:</strong> Create <code>reset.txt</code> on boot partition to restore hotspot mode</p>';
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
    echo '  const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");';
    echo '  if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");';
    echo '}';
    echo '</script>';
?>

</body>
</html>
