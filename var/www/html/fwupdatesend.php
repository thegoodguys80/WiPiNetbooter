<?php

header("Refresh: 3; url=fwupdate.php");
include 'menu_include.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

echo '<section><center><p>';

// SECURITY: Validate user inputs
$ip = $_GET["ip"] ?? '';
$version = $_GET["version"] ?? '';

// Validate IP address
if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    echo '<p><b>Invalid IP address</b></p>';
    exit;
}

// Whitelist firmware versions
$fwfile = '';
if ($version == '4.01') {
    $fwfile = 'FW_Netdimm_401.bin';
} elseif ($version == '4.02') {
    $fwfile = 'FW_Netdimm_402.bin';
} elseif ($version == '4.03') {
    $fwfile = 'FW_Netdimm_403.bin';
} else {
    echo '<p><b>Invalid firmware version</b></p>';
    exit;
}

echo '<p><b>' . htmlspecialchars($version, ENT_QUOTES, 'UTF-8') . ' Firmware upgrade file sent to ' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>Follow on-screen instructions to upgrade</b></p>';

// SECURITY: Use escapeshellarg for parameters
$command = 'sudo python /sbin/piforce/webforcefw.py ' . 
           escapeshellarg($fwfile) . ' ' . 
           escapeshellarg($ip);
$output = shell_exec($command);
// SECURITY: HTML escape output
echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8');

echo '</p><center></body></html>';

?>