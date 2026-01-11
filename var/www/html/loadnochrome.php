<?php

set_time_limit(0);
include 'menu_include.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

$relaymode = file_get_contents('/sbin/piforce/relaymode.txt');
$zeromode = file_get_contents('/sbin/piforce/zeromode.txt');
$openmode = file_get_contents('/sbin/piforce/openmode.txt');
$ffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');
// SECURITY: Validate user inputs
$rom = $_GET["rom"] ?? '';
$name = $_GET["name"] ?? '';
$dimm = $_GET["dimm"] ?? '';
$mapping = $_GET["mapping"] ?? '';
$ffb = $_GET["ffb"] ?? '';

// Validate ROM filename (basename only, no path traversal)
$rom = basename($rom);
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $rom)) {
    header("Location: gamelist.php");
    exit;
}

// Validate DIMM IP address
if (!filter_var($dimm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    header("Location: gamelist.php");
    exit;
}

// Validate device paths if provided
if ($mapping !== '' && !preg_match('/^\/dev\/[a-zA-Z0-9\/]+$/', $mapping)) {
    $mapping = '';
}
if ($ffb !== '' && !preg_match('/^\/dev\/[a-zA-Z0-9\/]+$/', $ffb)) {
    $ffb = '';
}

$rompath = '/boot/roms/' . $rom;

echo '<p>';

?>

<section><center>

<?php
// SECURITY: HTML escape output
echo '<h1><a href="gamelist.php?display=all#anchor' . urlencode($name) . '">Loading<br>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</a></h1></center>';
?>

<?php

// SECURITY: Use escapeshellarg for all parameters
$command = 'sudo python /sbin/piforce/webforce.py ' . 
           escapeshellarg($rom) . ' ' . 
           escapeshellarg($dimm) . ' ' . 
           escapeshellarg($relaymode) . ' ' . 
           escapeshellarg($zeromode) . ' ' . 
           escapeshellarg($mapping) . ' ' . 
           escapeshellarg($ffb);
$output = shell_exec($command . ' > /dev/null 2>/dev/null &');

$progress = 100;
while(is_int($progress) && $progress != 0 || $progress == 'COMPLETE'){
$handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
$progress = fgets($handle);
pclose($handle);
sleep(0.1);
}

?>

<script type="text/javascript">
<?php
// SECURITY: URL encode for JavaScript
echo 'setTimeout(function(){window.location="loadprogress.php?name=' . urlencode($name) . '";}, 1)'
?>
</script>