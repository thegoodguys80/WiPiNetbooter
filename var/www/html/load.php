<?php

set_time_limit(0);

include 'menu_include.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

function gzfilesize($filename) {
  $gzfs = FALSE;
  if(($zp = fopen($filename, 'r'))!==FALSE) {
    if(@fread($zp, 2) == "\x1F\x8B") { // this is a gzip'd file
      fseek($zp, -4, SEEK_END);
      if(strlen($datum = @fread($zp, 4))==4)
        extract(unpack('Vgzfs', $datum));
    }
    else // not a gzip'd file, revert to regular filesize function
      $gzfs = filesize($filename);
    fclose($zp);
  }
  return($gzfs);
}

// Read system configuration files
$relaymode = trim(file_get_contents('/sbin/piforce/relaymode.txt'));
$zeromode = trim(file_get_contents('/sbin/piforce/zeromode.txt'));
$openmode = trim(file_get_contents('/sbin/piforce/openmode.txt'));
$ffbmode = trim(file_get_contents('/sbin/piforce/ffbmode.txt'));

// SECURITY: Validate all user inputs to prevent command injection
$rom = $_GET["rom"] ?? '';
$name = $_GET["name"] ?? '';
$dimm = $_GET["dimm"] ?? '';
$mapping = $_GET["mapping"] ?? '';
$ffb = $_GET["ffb"] ?? '';

// Validate ROM filename (alphanumeric, underscore, dash, dot only)
if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.(bin|bin\.gz)$/i', $rom)) {
    die('Error: Invalid ROM filename format');
}

// Validate ROM exists
$rompath = '/boot/roms/' . basename($rom); // basename prevents path traversal
if (!file_exists($rompath)) {
    die('Error: ROM file not found');
}

// Validate IP address format
if (!filter_var($dimm, FILTER_VALIDATE_IP)) {
    die('Error: Invalid IP address format');
}

// Validate device paths (mapping and ffb)
if (!empty($mapping) && !preg_match('#^/dev/[a-zA-Z0-9_]+$#', $mapping)) {
    die('Error: Invalid mapping device path');
}
if (!empty($ffb) && !preg_match('#^/dev/input/event[0-9]+$#', $ffb)) {
    die('Error: Invalid FFB device path');
}

// Validate relay/zero modes are expected values
if (!in_array($relaymode, ['relayon', 'relayoff'])) {
    die('Error: Invalid relay mode');
}
if (!in_array($zeromode, ['hackon', 'hackoff'])) {
    die('Error: Invalid zero mode');
}

$filesize = gzfilesize($rompath);
ini_set('output_buffering', false);
$last = 0;

echo '<p>';

?>

<section><center>

<?php
// SECURITY: HTML escape display name to prevent XSS
echo '<h1>Loading<br>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</h1></center>';
?>

<div id="myProgress">
  <div id="myBar">0%</div>
</div>

<?php

// SECURITY: Build command with properly escaped arguments
// Note: webforce.py now has its own validation as well (defense in depth)
$command = 'sudo python3 /sbin/piforce/webforce.py ' . 
    escapeshellarg($rom) . ' ' . 
    escapeshellarg($dimm) . ' ' . 
    escapeshellarg($relaymode) . ' ' . 
    escapeshellarg($zeromode) . ' ' . 
    escapeshellarg($mapping) . ' ' . 
    escapeshellarg($ffb) . 
    ' > /dev/null 2>/dev/null &';

$output = shell_exec($command);

$progress = 0;
while($progress < 100) {
    $handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
    $progress = fgets($handle);
    if(($progress > $last && $progress < 100) || $progress == 10){
         echo '<script>';
         echo 'var elem = document.getElementById("myBar");';
         echo 'elem.style.width = '.$progress.' + "%";';
         echo 'elem.innerHTML = '.$progress.'  + "%";';
         echo '</script>';
    }
    $last = $progress;
    ob_flush(); 
    flush();
    sleep(0.1);
    pclose($handle);
}

echo '<script>';
echo 'var elem = document.getElementById("myBar");';
echo 'elem.style.width = 100 + "%";';
echo 'elem.innerHTML = 100  + "%";';
echo '</script>';
// SECURITY: URL encode name parameter
echo '<br><center><a href="gamelist.php?display=all#anchor' . urlencode($name) . '">LOADING COMPLETE</a></center>';
?>

<script type="text/javascript">
<?php
// SECURITY: JavaScript escape name parameter
echo 'setTimeout(function(){window.location="gamelist.php?display=all#anchor' . addslashes($name) . '";}, 2000)';
?>
</script>