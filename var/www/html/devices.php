<?php
include 'menu.php';
include 'devicelist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Device File Management</a></h1>';
echo '<table class="center" id="options">';
echo '<tr><th>Device File</th><th>Status</th><th>Actions</th></tr>';

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
?>