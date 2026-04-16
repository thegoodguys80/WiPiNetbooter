<?php
// Async WiFi status endpoint for sidebar
header('Content-Type: application/json');
header('Cache-Control: no-store');

$ssid = '';

// Try iwgetid (fast, always available on Pi)
$out = trim(@shell_exec('iwgetid -r 2>/dev/null') ?? '');
if ($out) {
    $ssid = $out;
} else {
    // Fallback: nmcli
    $out = @shell_exec('nmcli -t -f active,ssid dev wifi 2>/dev/null');
    if ($out) {
        foreach (explode("\n", trim($out)) as $line) {
            if (strpos($line, 'yes:') === 0) {
                $ssid = substr($line, 4);
                break;
            }
        }
    }
}

echo json_encode(['ssid' => $ssid ?: null]);
