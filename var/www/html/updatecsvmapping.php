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
$posted = $_POST['mapping'] ?? '';

$pieces = explode('#', $posted);
$rom = $pieces[0] ?? '';
$mapping = $pieces[1] ?? '';

// Validate ROM filename (basename only)
$rom = basename($rom);
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $rom)) {
    header("Location: editmappings.php");
    exit;
}

// Validate mapping filename pattern
if ($mapping !== '' && !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $mapping)) {
    header("Location: editmappings.php");
    exit;
}

while(($data = fgetcsv($input)) !== FALSE){
    if($data[1] == $rom){
        $data[14] = $mapping;
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
header ("Location: editmappings.php");
?>

