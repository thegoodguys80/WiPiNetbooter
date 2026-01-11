<?php
include 'auditscanresults.php';
$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$csvfile = 'csv/romsinfo.csv';
$rompath = '/boot/roms/';
$tempfile = tempnam(".", "tmp"); // produce a temporary file name, in the current directory

header("Refresh: 4; url=gamelist.php?display=all");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo 'Updating rom names to game list ....';
echo '</p><center></body></html>';

if(!$input = fopen($csvfile,'r')){
    die('could not open existing csv file');
}
if(!$output = fopen($tempfile,'w')){
    die('could not open temporary output file');
}

if ($_GET["rename"] == 'yes') {

for ($x = 1; $x <= $successes; $x++) {
  // SECURITY: Validate filenames using basename to prevent path traversal
  $oldname = $rompath . basename(${'filename'.$x});
  $newname = $rompath . basename(${'auditname'.$x});
  
  // SECURITY: Use escapeshellarg for each parameter
  $renamecmd = 'sudo python /sbin/piforce/renamecsv.py ' . 
               escapeshellarg($oldname) . ' ' . 
               escapeshellarg($newname) . ' ' . 
               escapeshellarg($lcdmode);
  shell_exec($renamecmd . ' > /dev/null 2>/dev/null &');
}

$i = 1;
while(($data = fgetcsv($input)) !== FALSE){
    $namecheck = ${'auditname'.$i};
    $filename = ${'filename'.$i};
    if ($data[17] == $namecheck) {
        $data[1] = $namecheck;
	if ($i < $successes){$i++;}
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

// SECURITY: Use escapeshellarg for parameters
$command = 'sudo python /sbin/piforce/renamecsv.py ' . 
           escapeshellarg($tempfile) . ' ' . 
           escapeshellarg($csvfile) . ' ' . 
           escapeshellarg($lcdmode);
shell_exec($command);
}
 
else{
$i = 1;
while(($data = fgetcsv($input)) !== FALSE){
    $namecheck = ${'auditname'.$i};
    $filename = ${'filename'.$i};
    if ($data[17] == $namecheck) {
        $data[1] = $filename;
	if ($i < $successes){$i++;}
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

// SECURITY: Use escapeshellarg for parameters
$command = 'sudo python /sbin/piforce/renamecsv.py ' . 
           escapeshellarg($tempfile) . ' ' . 
           escapeshellarg($csvfile) . ' ' . 
           escapeshellarg($lcdmode);
shell_exec($command);
}

?>