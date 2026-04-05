<?php
/**
 * Bulk enable/disable selected ROMs (POST from editgamelist.php).
 */
include_once __DIR__ . '/includes/romsinfo_enabled.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: editgamelist.php', true, 302);
    exit;
}

$action = isset($_POST['bulk_action']) ? trim((string) $_POST['bulk_action']) : '';
$roms = $_POST['roms'] ?? [];

if (!is_array($roms)) {
    $roms = [];
}

$val = null;
if ($action === 'enable') {
    $val = 'Yes';
} elseif ($action === 'disable') {
    $val = 'No';
}

if ($val === null) {
    header('Location: editgamelist.php', true, 302);
    exit;
}

$map = [];
foreach ($roms as $r) {
    $r = basename((string) $r);
    if (romsinfo_valid_rom_filename($r)) {
        $map[$r] = $val;
    }
}

if (!empty($map)) {
    romsinfo_set_enabled_batch($map);
}

header('Location: editgamelist.php', true, 302);
exit;
