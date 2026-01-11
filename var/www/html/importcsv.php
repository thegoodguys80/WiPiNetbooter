<?php

// SECURITY: Static file paths with no user input
$csvfile = '/var/www/html/csv/romsinfo.csv';
$newfile = '/boot/config/romsinfo.csv';

// SECURITY: Use escapeshellarg for parameters (even though static)
$command = 'sudo python /sbin/piforce/importcsv.py ' . 
           escapeshellarg($newfile) . ' ' . 
           escapeshellarg($csvfile);
shell_exec($command);
header ("Location: options.php");
?>

