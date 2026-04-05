<?php
include 'ui_mode.php';
include 'devicelist.php';

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Device Files</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container">';
    
    echo '<h1>'.arcade_icon('options').' OpenJVS Device Files</h1>';
    echo '<p style="margin-bottom: 24px;">Manage controller device configuration files for OpenJVS</p>';
    
    echo '<div style="margin-bottom: 24px;">';
    echo '<a href="openjvs.php" class="btn btn-primary">'.arcade_icon('setup').' OpenJVS Settings</a>';
    echo '<a href="devicescan.php" class="btn btn-secondary" style="margin-left: 8px;">'.arcade_icon('scan').' Scan Devices</a>';
    echo '<a href="deviceconfig.php" class="btn btn-secondary" style="margin-left: 8px;">'.arcade_icon('edit').' Create New</a>';
    echo '</div>';

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
    $command = 'sudo python3 /sbin/piforce/renamecsv.py ' . 
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
    
    $command = 'sudo python3 /sbin/piforce/renamecsv.py ' . 
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
    
    $command = 'sudo python3 /sbin/piforce/delete.py ' . escapeshellarg($deletefile);
    shell_exec($command);
    header('Location: devices.php');
    exit;
}

// SECURITY: Static command with no user input
$command = 'sudo python3 /sbin/piforce/devicefiles.py';
shell_exec($command);

$devicefiles = scandir('/etc/openjvs/devices');

    // Card grid
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
            echo '<a href="editor.php?devicefile=' . urlencode($devicefilepath) . '" class="btn btn-secondary btn-sm">'.arcade_icon('edit').' Edit</a> ';
            
            if ($status == 'disabled') {
                echo '<a href="devices.php?command=enable&file=' . urlencode($devicefilepath) . '" class="btn btn-primary btn-sm">✓ Enable</a> ';
            } else {
                echo '<a href="devices.php?command=disable&file=' . urlencode($devicefilepath) . '" class="btn btn-warning btn-sm">○ Disable</a> ';
            }
            
            echo '<a href="devices.php?command=delete&file=' . urlencode($devicefilepath) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this device file?\')">'.arcade_icon('trash').' Delete</a>';
            echo '</div>';
            
            echo '</div></div>';
        }
        
        echo '</div>';
    }
    
    echo '</div></div>'; // Close main-content and container
    
    // Add sidebar toggle script
    echo '<script>';
    echo 'function toggleSidebar() {';
    echo '  const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");';
    echo '  if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");';
    echo '}';
    echo '</script>';
    echo '</body></html>';
?>