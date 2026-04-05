<?php
/**
 * ROM filenames hidden from the default game list (dedupe / hide duplicates).
 * Storage: csv/gamelist_hidden.txt — one filename per line.
 */

function gamelist_hidden_file_path() {
    return __DIR__ . '/../csv/gamelist_hidden.txt';
}

function gamelist_valid_rom_filename($rom) {
    return (bool) preg_match('/^[a-zA-Z0-9_\-\.\{\}\[\]!]+\.(bin|bin\.gz)$/i', $rom);
}

/** @return string[] sorted unique ROM basenames */
function gamelist_hidden_load() {
    $p = gamelist_hidden_file_path();
    if (!is_readable($p)) {
        return [];
    }
    $lines = @file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }
    $out = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || (isset($line[0]) && $line[0] === '#')) {
            continue;
        }
        if (!gamelist_valid_rom_filename($line)) {
            continue;
        }
        $out[$line] = true;
    }
    return array_keys($out);
}

/** @param string[] $roms */
function gamelist_hidden_save(array $roms) {
    $p = gamelist_hidden_file_path();
    $roms = array_unique(array_map('trim', $roms));
    $roms = array_filter($roms, 'gamelist_valid_rom_filename');
    sort($roms);
    $body = "# ROM filenames hidden from the main game list (one per line)\n";
    foreach ($roms as $r) {
        $body .= $r . "\n";
    }
    @file_put_contents($p, $body, LOCK_EX);
}
