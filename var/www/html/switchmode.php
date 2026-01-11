<?php
header("Refresh: 1; url=options.php");
include 'menu_include.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
// SECURITY: Validate mode parameter
$mode = $_GET["mode"] ?? '';

// Whitelist allowed modes
$allowed_modes = ['single', 'menu', 'other_valid_modes']; // Add actual valid modes
if (!in_array($mode, $allowed_modes)) {
    // If mode validation needed, add proper whitelist
    // For now, sanitize to alphanumeric only
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $mode)) {
        header("Location: options.php");
        exit;
    }
}

echo 'Updating mode to ' . htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') . ' ...';
echo '</p><center></body></html>';

// SECURITY: Use escapeshellarg for parameter
$command = 'sudo python /sbin/piforce/switchmode.py ' . escapeshellarg($mode);
shell_exec($command . ' > /dev/null 2>/dev/null &');

?>