<?php
// SECURITY: Validate SSID and PSK inputs
$ssid = $_POST['ssid'] ?? '';
$psk = $_POST['psk'] ?? '';

// Validate SSID (max 32 chars, alphanumeric)
if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
    header("Location: wifi.php");
    exit;
}

// Validate PSK (8-63 chars)
if (strlen($psk) < 8 || strlen($psk) > 63) {
    header("Location: wifi.php");
    exit;
}

// SECURITY: Use escapeshellarg for parameters
$command = 'sudo python /sbin/piforce/hotspotwifi.py ' . 
           escapeshellarg($ssid) . ' ' . 
           escapeshellarg($psk);
shell_exec($command);
header("Location: wifi.php");
exit;
?>

