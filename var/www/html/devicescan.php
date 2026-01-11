<?php
// SECURITY: Static command with no user input
$command = 'sudo python /sbin/piforce/devicelist.py';
shell_exec($command);
include 'menu.php';
include 'devicelist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Device Scan</a></h1><br>';
echo 'Number of devices found: '.$devices.'<br><br>';
echo 'Please select a device below to configure it for OpenJVS<br>';
echo '<br><a href="devicescan.php">Rescan Devices</a><br>';
echo '<br><table class="center" id="options">';
echo '<tr><th>Device Name</th><th>Device Path</th><th>Config File</th><th>Actions</th></tr>';

for ($i = 1; $i <= $devices; $i++) {

$filename = ${'file'.$i};
$disabledfilename = $filename.'.disabled';

if ($_GET["command"] == 'rescan') {
header ("Location: devicescan.php");
}

if ($_GET["command"] == 'enable') {
    // SECURITY: Validate file parameter
    $enablefile = $_GET["file"] ?? '';
    
    // Validate it's a .disabled file in the correct directory
    $enablefile = basename($enablefile);
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.disabled$/', $enablefile)) {
        header("Location: devicescan.php");
        exit;
    }
    
    $without_extension = substr($enablefile, 0, strrpos($enablefile, "."));
    
    // SECURITY: Use escapeshellarg for parameters
    $command = 'sudo python /sbin/piforce/renamecsv.py ' . 
               escapeshellarg($enablefile) . ' ' . 
               escapeshellarg($without_extension);
    shell_exec($command);
    header("Location: devicescan.php");
    exit;
}

if ($_GET["command"] == 'disable') {
    // SECURITY: Validate file parameter
    $disablefile = $_GET["file"] ?? '';
    
    // Validate filename pattern
    $disablefile = basename($disablefile);
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $disablefile)) {
        header("Location: devicescan.php");
        exit;
    }
    
    // SECURITY: Use escapeshellarg for parameters
    $command = 'sudo python /sbin/piforce/renamecsv.py ' . 
               escapeshellarg($disablefile) . ' ' . 
               escapeshellarg($disablefile . '.disabled');
    shell_exec($command);
    header("Location: devicescan.php");
    exit;
}

if ($_GET["command"] == 'delete') {
    // SECURITY: Validate file parameter
    $deletefile = $_GET["file"] ?? '';
    
    // Validate filename pattern
    $deletefile = basename($deletefile);
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $deletefile)) {
        header("Location: devicescan.php");
        exit;
    }
    
    // SECURITY: Use escapeshellarg for parameter
    $command = 'sudo python /sbin/piforce/delete.py ' . escapeshellarg($deletefile);
    shell_exec($command);
    header("Location: devicescan.php");
    exit;
}

if (file_exists($filename)) {
    $status = 'enabled';
} else if (file_exists($disabledfilename)) {
    $status = 'disabled';
} else {
    $status = 'not found';
}

echo '<tr>';
// SECURITY: HTML escape output
echo '<td>' . htmlspecialchars(${'name'.$i}, ENT_QUOTES, 'UTF-8') . '</td>';
echo '<td>' . htmlspecialchars(${'path'.$i}, ENT_QUOTES, 'UTF-8') . '</td>';
if ($status == 'enabled') {
echo '<td><b>'.$status.'</b></td>';}
else {echo '<td>'.$status.'</td>';}
// SECURITY: URL encode parameters
if ($status == 'disabled'){
    echo '<td><a href="devicescan.php?command=enable&file=' . urlencode($disabledfilename) . '">enable</a> / <a href="devicescan.php?command=delete&file=' . urlencode($disabledfilename) . '">delete</a></td>';
}
else if ($status == 'enabled'){
    echo '<td><a href="devicescan.php?command=disable&file=' . urlencode($filename) . '">disable</a>';
}
else {
    echo '<td><a href="deviceconfig.php?path=' . urlencode(${'path'.$i}) . '">configure</a></td>';
}
echo "</tr>";
}
echo '</table>';
echo '</p><center></body></html>';
?>