<?php
header('Content-type: image/jpeg');

$name = $_GET["name"] ?? '';
$mode = $_GET["mode"] ?? '';

// Whitelist allowed modes
$allowed_modes = [
    'fzero' => ['image' => 'img/FZAX.jpg', 'path' => '/boot/config/cards/fzero/'],
    'mkgp'  => ['image' => 'img/MKGP.jpg', 'path' => '/boot/config/cards/mkgp/'],
    'wmmt'  => ['image' => 'img/WMMT.jpg', 'path' => '/boot/config/cards/wmmt/'],
];
if (!isset($allowed_modes[$mode])) {
    http_response_code(400);
    exit;
}

// Reject path traversal and unsafe characters in name
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $name) || strpos($name, '..') !== false) {
    http_response_code(400);
    exit;
}

$cardimage = imagecreatefromjpeg($allowed_modes[$mode]['image']);
$path = $allowed_modes[$mode]['path'];

$textcolour = imagecolorallocate($cardimage, 0, 0, 0);
$namefont_path = 'img/Barlow-Light.ttf';
$font_path = 'img/BarlowCondensed-Light.ttf';

if ($mode == "wmmt" || $mode == "mkgp"){
$textcolour = imagecolorallocate($cardimage, 153,50,204);
$font_path = 'img/BarlowCondensed-Light.ttf';
}

$saved ="LAST SAVE:";
$filename = $path . basename($name);
$lastModifiedTimestamp = filemtime($filename);
$date =date("M d Y", $lastModifiedTimestamp);
$time =date("H:i", $lastModifiedTimestamp);

if ($mode == "fzero"){
$textsize=15;
$angle=0;
$left=35;
$texttop=81;
$savedsize=15;
$savedtop=118;
$datesize=15;
$datetop=138;
$timeleft=120;
$timesize=15;
$timetop=138;
}

if ($mode == "mkgp"){
$textsize=24;
$angle=0;
$left=60;
$texttop=72;
$savedsize=16;
$savedtop=97;
$datesize=16;
$datetop=117;
$timeleft=150;
$timesize=16;
$timetop=117;
}

if ($mode == "wmmt"){
$textsize=24;
$angle=0;
$left=50;
$texttop=70;
$savedsize=20;
$savedtop=102;
$datesize=20;
$datetop=137;
$timeleft=50;
$timesize=20;
$timetop=172;
}

imagettftext($cardimage, $textsize,$angle,$left,$texttop, $textcolour, $namefont_path, strtoupper($name));
imagettftext($cardimage, $datesize,$angle,$left,$datetop, $textcolour, $font_path, strtoupper($date));
imagettftext($cardimage, $savedsize,$angle,$left,$savedtop, $textcolour, $font_path, $saved);
imagettftext($cardimage, $timesize,$angle,$timeleft,$timetop, $textcolour, $font_path, $time);


imagejpeg($cardimage);
imagedestroy($cardimage);

?>