<?php
/**
 * Toggle romsinfo.csv "enabled" column (index 12) for a single ROM (GET).
 */
include_once __DIR__ . '/includes/romsinfo_enabled.php';

$rom = isset($_GET['rom']) ? basename((string) $_GET['rom']) : '';
$enabled = $_GET['enabled'] ?? '';

if (!romsinfo_valid_rom_filename($rom)) {
    header('Location: editgamelist.php');
    exit;
}

$e = strtolower(trim((string) $enabled));
if (in_array($e, ['yes', '1', 'true'], true)) {
    $enabledValue = 'Yes';
} elseif (in_array($e, ['no', '0', 'false'], true)) {
    $enabledValue = 'No';
} else {
    header('Location: editgamelist.php');
    exit;
}

romsinfo_set_enabled_batch([$rom => $enabledValue]);

header('Location: editgamelist.php');
exit;
