<?php

header("Refresh: 2; url=cardemulator.php?mode=main");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
// SECURITY: Validate all user inputs
$card = $_GET["card"] ?? '';
$mode = $_GET["mode"] ?? '';
$launchmode = $_GET["launchmode"] ?? '';

// Validate card filename (alphanumeric, underscore, dash, dot only)
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $card)) {
    die('Error: Invalid card filename');
}

// Validate mode (whitelist of allowed modes)
$allowed_modes = ['idas', 'id2', 'id3', 'fzero', 'mkgp', 'wmmt'];
if (!in_array($mode, $allowed_modes)) {
    die('Error: Invalid card mode');
}

// Validate launch mode
if (!in_array($launchmode, ['manual', 'auto'])) {
    die('Error: Invalid launch mode');
}
$emuport = '';
$devices = array();
$devices = glob('/dev' . '/ttyUSB*');
$dropfolder = '/var/log/activecard';
$isdirempty = !(new \FilesystemIterator($dropfolder))->valid();

if ($mode == 'idas' || $mode == 'id2' || $mode == 'id3'){
$emumode = 'id';
}
else{
$emumode = $mode;
}

if (readlink("/dev/COM1")){
echo 'COM1 is present - checking ports<br>';
$comport = readlink("/dev/COM1");
$compath = '/dev/'.$comport;
// SECURITY: HTML escape output
echo 'COM1 path: ' . htmlspecialchars($compath, ENT_QUOTES, 'UTF-8') . '<br>';
foreach ($devices as $device) {
    if ($device != $compath){
       $emuport = $device;
    }
  }
}
else{
$emuport = '/dev/ttyUSB0';
}

if ($launchmode == "manual"){

if(empty($devices) || $emuport == null){
   echo '<br><b>No serial adaptor detected<br>';
   echo 'Please check connections</b>';}
else {
// SECURITY: HTML escape output
echo '<br>Card emulator will launch on: ' . htmlspecialchars($emuport, ENT_QUOTES, 'UTF-8') . '<br>';
echo '<b>Starting card emulator with card ' . htmlspecialchars($card, ENT_QUOTES, 'UTF-8') . '</b>';

// SECURITY: Validate device path
if (!preg_match('#^/dev/ttyUSB[0-9]+$#', $emuport)) {
    die('Error: Invalid serial device path');
}

// SECURITY: Build commands with escaped arguments
$cardlog_path = '/boot/config/cards/' . escapeshellarg($mode) . '/' . escapeshellarg($card);
$command1 = 'sudo python /sbin/piforce/card_emulator/cardlog.py ' . escapeshellarg($cardlog_path);
shell_exec($command1 . ' > /dev/null 2>/dev/null &');

if ($emumode == 'id') {
    $command2 = 'sudo python3 /sbin/piforce/card_emulator/' . escapeshellarg($emumode . 'cardemu.py') . 
                ' -cp ' . escapeshellarg($emuport) . 
                ' -f ' . escapeshellarg('/boot/config/cards/' . $mode . '/' . $card) . 
                ' -m ' . escapeshellarg($mode);
} else {
    $command2 = 'sudo python3 /sbin/piforce/card_emulator/' . escapeshellarg($emumode . 'cardemu.py') . 
                ' -cp ' . escapeshellarg($emuport) . 
                ' -f ' . escapeshellarg('/boot/config/cards/' . $mode . '/' . $card);
}
shell_exec($command2 . ' > /dev/null 2>/dev/null &');
}
}

if ($launchmode == "auto"){
exec("ps -ax | grep -i cardemu | grep -v grep", $pids);
if (empty($pids)) {
    echo '<br><b><p style="color:red">Card emulator is not running</p></b>';
    echo '<b>Card cannot be inserted yet!</b>';
}
elseif ($isdirempty){
echo '<br><b><p style="color:green">Card emulator ready</p></b>';
echo '<b>Inserting card ...</b>';

// SECURITY: Use escaped arguments for cp command
$source = '/boot/config/cards/' . $mode . '/' . $card;
$dest = $dropfolder . '/' . $card;

// Verify source file exists
if (!file_exists($source)) {
    die('Error: Card file not found');
}

$insertcommand = 'sudo cp ' . escapeshellarg($source) . ' ' . escapeshellarg($dest);
shell_exec($insertcommand . ' > /dev/null 2>/dev/null &');
file_put_contents('/sbin/piforce/nfcwriteback.txt','no');
}
else {
    echo '<br><b><p style="color:red">Existing card detected</p></b>';
    echo '<b>Card cannot be inserted yet!</b>';
}
}

?>