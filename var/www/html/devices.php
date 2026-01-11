<?php
include 'ui_mode.php';
$ui_mode = get_ui_mode();

if ($ui_mode !== 'modern') {
    include 'menu_include.php';
}
include 'devicelist.php';

if ($ui_mode === 'modern') {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Device Files</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '</head><body>';
    
    // Sidebar navigation
    echo '<div class="sidebar" id="sidebarNav">';
    echo '<div class="sidebar-header">';
    echo '<h2>WiPi Netbooter</h2>';
    echo '</div>';
    echo '<nav class="sidebar-nav">';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
    <a href="gamelist.php" class="nav-item"><span class="nav-icon">🎮</span> Games</a>';
    echo '<a href="dimms.php" class="nav-item"><span class="nav-icon">🖥️</span> NetDIMMs</a>';
    echo '<a href="setup.php" class="nav-item active"><span class="nav-icon">⚙️</span> Setup</a>';
    echo '<a href="menu.php" class="nav-item"><span class="nav-icon">📋</span> Menu</a>';
    echo '</nav>';
    echo '</div>';
    
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    
    echo '<div class="container">';
    echo '<div class="main-content">';
    echo '<button class="burger-btn" id="burgerBtn" onclick="toggleSidebar()">';
    echo '<span></span><span></span><span></span>';
    echo '</button>';
    
    echo '<h1>🎛️ OpenJVS Device Files</h1>';
    echo '<p style="margin-bottom: 24px;">Manage controller device configuration files for OpenJVS</p>';
    
    echo '<div style="margin-bottom: 24px;">';
    echo '<a href="openjvs.php" class="btn btn-primary">⚙️ OpenJVS Settings</a>';
    echo '<a href="devicescan.php" class="btn btn-secondary" style="margin-left: 8px;">🔍 Scan Devices</a>';
    echo '<a href="deviceconfig.php" class="btn btn-secondary" style="margin-left: 8px;">➕ Create New</a>';
    echo '</div>';
} else {
    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
    echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
    echo '<section><center><p>';
    echo '<h1><a href="openjvs.php">OpenJVS Device File Management</a></h1>';
    echo '<table class="center" id="options">';
    echo '<tr><th>Device File</th><th>Status</th><th>Actions</th></tr>';
}

// SECURITY: Validate inputs for enable command
if (($_GET["command"] ?? '') == 'enable') {
    $enablefile = $_GET["file"] ?? '';
    
    // Validate file path (must be in /etc/openjvs/devices/)
    $realpath = realpath($enablefile);
    if ($realpath === false || strpos($realpath, '/etc/openjvs/devices/') !== 0) {
        die('Error: Invalid file path');
    }
    
    // Validate filename pattern
    $basename = basename($enablefile);
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.disabled$/', $basename)) {
        die('Error: Invalid filename');
    }
    
    $without_extension = substr($basename, 0, strrpos($basename, "."));
    $command = 'sudo python /sbin/piforce/renamecsv.py ' . 
               escapeshellarg($enablefile) . ' ' . 
               escapeshellarg($without_extension);
    shell_exec($command);
    header('Location: devices.php');
    exit;
}

// SECURITY: Validate inputs for disable command
if (($_GET["command"] ?? '') == 'disable') {
    $disablefile = $_GET["file"] ?? '';
    
    // Validate file path
    $realpath = realpath($disablefile);
    if ($realpath === false || strpos($realpath, '/etc/openjvs/devices/') !== 0) {
        die('Error: Invalid file path');
    }
    
    // Validate filename pattern (no .disabled extension)
    $basename = basename($disablefile);
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $basename) || strpos($basename, '.disabled') !== false) {
        die('Error: Invalid filename');
    }
    
    $command = 'sudo python /sbin/piforce/renamecsv.py ' . 
               escapeshellarg($disablefile) . ' ' . 
               escapeshellarg($disablefile . '.disabled');
    shell_exec($command);
    header('Location: devices.php');
    exit;
}

// SECURITY: Validate inputs for delete command
if (($_GET["command"] ?? '') == 'delete') {
    $deletefile = $_GET["file"] ?? '';
    
    // Validate file path
    $realpath = realpath($deletefile);
    if ($realpath === false || strpos($realpath, '/etc/openjvs/devices/') !== 0) {
        die('Error: Invalid file path');
    }
    
    // Validate filename
    $basename = basename($deletefile);
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $basename)) {
        die('Error: Invalid filename');
    }
    
    $command = 'sudo python /sbin/piforce/delete.py ' . escapeshellarg($deletefile);
    shell_exec($command);
    header('Location: devices.php');
    exit;
}

// SECURITY: Static command with no user input
$command = 'sudo python /sbin/piforce/devicefiles.py';
shell_exec($command);

$devicefiles = scandir('/etc/openjvs/devices');

if ($ui_mode === 'modern') {
    // Modern UI - Card grid
    if (count($devicefiles) <= 2) {
        echo '<div class="alert alert-info">No device files found. Create one using the "Create New" button above.</div>';
    } else {
        echo '<div class="grid grid-cols-3">';
        
        for ($i = 2; $i < count($devicefiles); $i++) {
            $devicefilename = $devicefiles[$i];
            $devicefilepath = '/etc/openjvs/devices/'.$devicefilename;
            
            $file_parts = pathinfo($devicefilename);
            $status = ($file_parts['extension'] == 'disabled') ? 'disabled' : 'enabled';
            
            echo '<div class="card">';
            echo '<div class="card-header">';
            echo '<h3 class="card-title">' . htmlspecialchars($devicefilename, ENT_QUOTES, 'UTF-8') . '</h3>';
            echo '</div>';
            echo '<div class="card-body">';
            
            if ($status == 'enabled') {
                echo '<span class="badge badge-success">✓ Enabled</span>';
            } else {
                echo '<span class="badge badge-secondary">○ Disabled</span>';
            }
            
            echo '<div style="margin-top: 16px;">';
            echo '<a href="editor.php?devicefile=' . urlencode($devicefilepath) . '" class="btn btn-secondary btn-sm">✏️ Edit</a> ';
            
            if ($status == 'disabled') {
                echo '<a href="devices.php?command=enable&file=' . urlencode($devicefilepath) . '" class="btn btn-primary btn-sm">✓ Enable</a> ';
            } else {
                echo '<a href="devices.php?command=disable&file=' . urlencode($devicefilepath) . '" class="btn btn-warning btn-sm">○ Disable</a> ';
            }
            
            echo '<a href="devices.php?command=delete&file=' . urlencode($devicefilepath) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this device file?\')">🗑️ Delete</a>';
            echo '</div>';
            
            echo '</div></div>';
        }
        
        echo '</div>';
    }
    
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
    echo '</body></html>';
} else {
    // Classic UI - Table
    for ($i = 2; $i < count($devicefiles); $i++) {
        $devicefilename = $devicefiles[$i];
        $devicefilepath = '/etc/openjvs/devices/'.$devicefilename;
        
        $file_parts = pathinfo($devicefilename);
        if ($file_parts['extension'] == 'disabled') {
            $status = 'disabled';}
        else {$status = 'enabled';}
        
        echo '<tr>';
        // SECURITY: HTML escape output
        echo '<td>' . htmlspecialchars($devicefilename, ENT_QUOTES, 'UTF-8') . '</td>';
        if ($status == 'enabled'){
            echo '<td><b>'.$status.'</b></td>';}
        else {
            echo '<td>'.$status.'</td>';}
        // SECURITY: URL encode file paths in links
        if ($status == 'disabled') {
            echo '<td><a href="editor.php?devicefile=' . urlencode($devicefilepath) . '">edit</a> / ' . 
                 '<a href="devices.php?command=enable&file=' . urlencode($devicefilepath) . '">enable</a> / ' . 
                 '<a href="devices.php?command=delete&file=' . urlencode($devicefilepath) . '">delete</a></td>';
        } else {
            echo '<td><a href="editor.php?devicefile=' . urlencode($devicefilepath) . '">edit</a> / ' . 
                 '<a href="devices.php?command=disable&file=' . urlencode($devicefilepath) . '">disable</a> / ' . 
                 '<a href="devices.php?command=delete&file=' . urlencode($devicefilepath) . '">delete</a></td>';
        }
        echo "</tr>";
    }
    echo '</p><center></body></html>';
}
?>