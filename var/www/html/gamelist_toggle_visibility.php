<?php
/**
 * Hide or unhide a ROM from the default game list (GET redirect).
 */
include_once __DIR__ . '/includes/gamelist_visibility.php';

$rom = isset($_GET['rom']) ? trim((string) $_GET['rom']) : '';
$action = isset($_GET['action']) ? trim((string) $_GET['action']) : '';
$return_show_hidden = isset($_GET['show_hidden']) && $_GET['show_hidden'] === '1';

if (!gamelist_valid_rom_filename($rom) || ($action !== 'hide' && $action !== 'unhide')) {
    header('Location: gamelist.php', true, 302);
    exit;
}

$set = array_flip(gamelist_hidden_load());
if ($action === 'hide') {
    $set[$rom] = true;
} else {
    unset($set[$rom]);
}
gamelist_hidden_save(array_keys($set));

$q = $return_show_hidden ? '?show_hidden=1' : '';
header('Location: gamelist.php' . $q, true, 302);
exit;
