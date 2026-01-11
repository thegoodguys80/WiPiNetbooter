<?php

header("Refresh: 2; url=openjvscontrol.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
// SECURITY: Validate device path input
$mapping = $_POST['mapping'] ?? '';

// Validate device path (must be /dev/ttyUSB* or /dev/ttyACM*)
if (!preg_match('#^/dev/(ttyUSB|ttyACM)[0-9]+$#', $mapping)) {
    die('Error: Invalid device path. Must be /dev/ttyUSB* or /dev/ttyACM*');
}

// Verify device exists
if (!file_exists($mapping)) {
    die('Error: Device not found: ' . htmlspecialchars($mapping, ENT_QUOTES, 'UTF-8'));
}

// SECURITY: HTML escape output
echo '<br><br>Starting OpenJVS with mapping<br><b>' . htmlspecialchars($mapping, ENT_QUOTES, 'UTF-8') . '</b>';

// SECURITY: Use secure command execution with escaped arguments
$opencommand1 = 'sudo killall -9 openjvs';
shell_exec($opencommand1 . ' > /dev/null 2>/dev/null &');

$opencommand2 = 'sudo openjvs ' . escapeshellarg($mapping);
shell_exec($opencommand2 . ' > /dev/null 2>/dev/null &');

?>