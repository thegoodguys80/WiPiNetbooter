<?php

$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$csvfile = '/var/www/html/csv/romsinfo.csv';
$tempfile = tempnam(".", "tmp"); // produce a temporary file name, in the current directory

if(!$input = fopen($csvfile,'r')){
    die('could not open existing csv file');
}
if(!$output = fopen($tempfile,'w')){
    die('could not open temporary output file');
}

// SECURITY: Validate user inputs
$rom = $_GET['rom'] ?? '';
$fave = $_GET['fave'] ?? '';

// Validate ROM filename (basename only, no path traversal)
$rom = basename($rom);
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $rom)) {
    header("Location: gamelist.php");
    exit;
}

// Validate fave value (should be boolean-like)
if (!in_array($fave, ['0', '1', 'true', 'false', 'yes', 'no'])) {
    header("Location: gamelist.php?filename=$rom");
    exit;
}

while(($data = fgetcsv($input)) !== FALSE){
    if($data[1] == $rom){
        $data[13] = $fave;
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

// SECURITY: Use escapeshellarg for parameters
$command = 'sudo python3 /sbin/piforce/renamecsv.py ' . 
           escapeshellarg($tempfile) . ' ' . 
           escapeshellarg($csvfile) . ' ' . 
           escapeshellarg($lcdmode);
shell_exec($command);
header ("Location: gamelist.php?filename=$rom");
?>

