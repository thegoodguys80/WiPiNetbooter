<?php
include 'ui_mode.php';

echo '<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><title>WiPi Netbooter - Main Menu</title>';
    load_ui_styles();
    echo '</head><body>';

    echo modern_sliding_sidebar_nav('dashboard');
    echo '<div class="container p-6">';

    // Header with INSERT COIN ticker
    echo '<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:6px;">';
    echo '<h1 class="text-3xl" style="margin:0;">'.arcade_icon('home').' Dashboard</h1>';
    echo '<span class="insert-coin">&#9654; INSERT COIN</span>';
    echo '</div>';
    echo '<p style="color:var(--color-text-secondary);margin-bottom:24px;font-size:13px;letter-spacing:0.04em;">WIPI NETBOOTER &mdash; SEGA ARCADE NETBOOT SYSTEM</p>';
    
    // NetDIMM live status strip
    $dimms_list = [];
    $dimms_csv_path = __DIR__ . '/csv/dimms.csv';
    if (file_exists($dimms_csv_path)) {
        $fh = fopen($dimms_csv_path, 'r');
        fgetcsv($fh); // skip header
        while (($row = fgetcsv($fh)) !== false) {
            if (!empty($row[1]) && filter_var(trim($row[1]), FILTER_VALIDATE_IP)) {
                $dimms_list[] = ['name' => $row[0], 'ip' => trim($row[1])];
            }
        }
        fclose($fh);
    }

    if (!empty($dimms_list)) {
        echo '<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:24px;">';
        foreach ($dimms_list as $d) {
            $ip   = htmlspecialchars($d['ip'],   ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8');
            $id   = 'ndstatus_' . preg_replace('/[^a-z0-9]/i', '_', $d['ip']);
            echo '<div class="netdimm-status-card">';
            echo '<span id="'.$id.'_dot" class="netdimm-status-dot" aria-hidden="true">&#9679;</span>';
            echo '<div>';
            echo '<div class="netdimm-status-name">'.$name.'</div>';
            echo '<div class="netdimm-status-ip">'.$ip.'</div>';
            echo '</div>';
            echo '<span id="'.$id.'_badge" class="netdimm-status-badge" style="color:var(--color-text-tertiary);">…</span>';
            echo '</div>';
        }
        echo '</div>';

        $ips_js = json_encode(array_map(fn($d) => ['ip' => $d['ip'], 'id' => 'ndstatus_' . preg_replace('/[^a-z0-9]/i', '_', $d['ip'])], $dimms_list));
        echo '<script>(function(){const dimms='.$ips_js.';dimms.forEach(d=>{';
        echo 'fetch("pingtest.php?ip="+encodeURIComponent(d.ip)).then(r=>r.json()).then(data=>{';
        echo '  const dot=document.getElementById(d.id+"_dot");';
        echo '  const badge=document.getElementById(d.id+"_badge");';
        echo '  if(dot){dot.classList.toggle("online",data.online);dot.classList.toggle("offline",!data.online);}';
        echo '  if(badge){badge.textContent=data.online?"ONLINE":"OFFLINE";badge.style.color=data.online?"var(--arcade-green,#00ff88)":"var(--arcade-red,#ff2244)";}';
        echo '}).catch(()=>{const b=document.getElementById(d.id+"_badge");if(b){b.textContent="ERR";b.style.color="var(--color-warning)";}});';
        echo '});})();</script>';
    }

    // Recently Played
    $last_rom = '';
    $last_game_title = '';
    $last_game_image = '';
    $last_game_system = '';
    $last_game_mapping = '';
    $last_game_ffb = '';
    $log_file = '/var/www/logs/log.txt';
    if (file_exists($log_file)) {
        $log_raw = trim(file_get_contents($log_file));
        $last_rom = explode(' ', $log_raw)[0] ?? '';
        if ($last_rom && file_exists('csv/romsinfo.csv')) {
            $fh = fopen('csv/romsinfo.csv', 'r');
            fgetcsv($fh); // skip header
            while (($row = fgetcsv($fh)) !== false) {
                if (isset($row[1]) && $row[1] === $last_rom) {
                    $last_game_title   = $row[4]  ?? '';
                    $last_game_image   = $row[2]  ?? '';
                    $last_game_system  = $row[0]  ?? '';
                    $last_game_mapping = $row[14] ?? '';
                    $last_game_ffb     = $row[15] ?? '';
                    break;
                }
            }
            fclose($fh);
        }
    }
    if ($last_game_title) {
        $launch_url = 'loadcheck.php?rom='.urlencode($last_rom).'&name='.urlencode($last_game_title).'&system='.urlencode($last_game_system).'&mapping='.urlencode($last_game_mapping).'&ffb='.urlencode($last_game_ffb);
        echo '<div class="last-played-card">';
        echo '<img src="images/'.htmlspecialchars($last_game_image, ENT_QUOTES, 'UTF-8').'" style="width:72px;height:72px;object-fit:contain;border-radius:6px;background:rgba(0,212,255,0.05);border:1px solid rgba(0,212,255,0.2);flex-shrink:0;padding:4px;" onerror="this.style.display=\'none\'">';
        echo '<div style="flex:1;min-width:0;">';
        echo '<div class="last-played-label">'.arcade_icon('lastgame').' Last Played</div>';
        echo '<div class="last-played-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'.htmlspecialchars($last_game_title, ENT_QUOTES, 'UTF-8').'</div>';
        echo '<div class="last-played-system">'.htmlspecialchars($last_game_system, ENT_QUOTES, 'UTF-8').'</div>';
        echo '</div>';
        echo '<a href="'.htmlspecialchars($launch_url, ENT_QUOTES, 'UTF-8').'" class="btn-launch" style="width:auto;padding:10px 20px;flex-shrink:0;text-decoration:none;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">'.arcade_icon('rocket').' LAUNCH</a>';
        echo '</div>';
    }

    // Games Section
    echo '<h2 class="section-heading">'.arcade_icon('games').' Games</h2>';
    echo '<div class="grid grid-cols-3 mb-8">';
    
    echo '<a href="gamelist.php?display=all" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('library').' Game Library</h3>';
    echo '<p>Browse and launch all available games</p>';
    echo '</div></a>';
    
    echo '<a href="gamelist.php?display=faves" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('favorites').' Favourites</h3>';
    echo '<p>Quick access to your favorite games</p>';
    echo '</div></a>';
    
    echo '<a href="editgamelist.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('edit').' Manage Games</h3>';
    echo '<p>Enable or disable games in your library</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // NetDIMM Section
    echo '<h2 class="section-heading">'.arcade_icon('netdimms').' NetDIMM</h2>';
    echo '<div class="grid grid-cols-2 mb-8">';
    
    echo '<a href="dimms.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('globe').' NetDIMM Manager</h3>';
    echo '<p>Configure and manage your NetDIMM boards</p>';
    echo '</div></a>';
    
    echo '<a href="dimmscanner.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('scan').' Scan Network</h3>';
    echo '<p>Discover NetDIMM boards on your network</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // Setup & Configuration Section
    echo '<h2 class="section-heading">'.arcade_icon('setup').' Setup &amp; configuration</h2>';
    echo '<div class="grid grid-cols-3 mb-8">';
    
    echo '<a href="options.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('options').' Options</h3>';
    echo '<p>System settings and preferences</p>';
    echo '</div></a>';
    
    echo '<a href="network.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('network').' Network</h3>';
    echo '<p>Network configuration and status</p>';
    echo '</div></a>';
    
    echo '<a href="openjvs.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('gamepad').' OpenJVS</h3>';
    echo '<p>Controller and input configuration</p>';
    echo '</div></a>';
    
    echo '<a href="openffb.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('wheel').' Force Feedback</h3>';
    echo '<p>Force feedback wheel configuration</p>';
    echo '</div></a>';
    
    echo '<a href="cardemulator.php?mode=main" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('card').' Card Emulator</h3>';
    echo '<p>Arcade card reader emulation</p>';
    echo '</div></a>';
    
    echo '<a href="cardmanagement.php?mode=main" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('cards').' Card Management</h3>';
    echo '<p>Manage saved card data</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    // Tools & Utilities Section
    echo '<h2 class="section-heading">'.arcade_icon('tools').' Tools &amp; utilities</h2>';
    echo '<div class="grid grid-cols-3 mb-8">';
    
    echo '<a href="help.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('help').' Help</h3>';
    echo '<p>Documentation and guides</p>';
    echo '</div></a>';
    
    echo '<a href="dumpcsv.php" class="card card-interactive dashboard-card-link">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('romdb').' ROM Database</h3>';
    echo '<p>View ROM information database</p>';
    echo '</div></a>';
    
    echo '<a href="shutdown.php" class="card card-interactive dashboard-card-link dashboard-card-link--danger">';
    echo '<div class="card-body">';
    echo '<h3>'.arcade_icon('power').' Shutdown</h3>';
    echo '<p>Power off the system safely</p>';
    echo '</div></a>';
    
    echo '</div>';
    
    echo '</div>'; // container
    echo '</div>'; // main-content
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';
?>
