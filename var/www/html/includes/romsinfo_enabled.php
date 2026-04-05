<?php
/**
 * Shared helpers to update romsinfo.csv column 12 (enabled) for one or many ROMs.
 */

function romsinfo_valid_rom_filename($rom) {
    $rom = basename((string) $rom);

    return (bool) preg_match('/^[a-zA-Z0-9_\-\.\{\}\[\]!]+\.(bin|bin\.gz)$/i', $rom);
}

/**
 * Set enabled column for one or more ROM rows in one CSV write.
 *
 * @param array<string, string> $map ROM basename => 'Yes' or 'No'
 * @return bool true if the file was replaced successfully
 */
function romsinfo_set_enabled_batch(array $map) {
    $clean = [];
    foreach ($map as $rom => $val) {
        if (!romsinfo_valid_rom_filename($rom)) {
            continue;
        }
        $rom = basename($rom);
        if ($val === 'Yes' || $val === 'No') {
            $clean[$rom] = $val;
        }
    }
    if (empty($clean)) {
        return false;
    }

    $lcdmode = trim(@file_get_contents('/sbin/piforce/lcdmode.txt') ?: '');
    $csvfile = '/var/www/html/csv/romsinfo.csv';

    $tempfile = tempnam(sys_get_temp_dir(), 'wi_csv_');
    if ($tempfile === false) {
        return false;
    }

    $input = fopen($csvfile, 'r');
    if ($input === false) {
        @unlink($tempfile);

        return false;
    }

    $output = fopen($tempfile, 'w');
    if ($output === false) {
        fclose($input);
        @unlink($tempfile);

        return false;
    }

    while (($data = fgetcsv($input)) !== false) {
        if (isset($data[1]) && isset($clean[$data[1]])) {
            $data[12] = $clean[$data[1]];
        }
        fputcsv($output, $data);
    }

    fclose($input);
    fclose($output);

    $applied = false;

    if (@rename($tempfile, $csvfile)) {
        @chmod($csvfile, 0666);
        $applied = true;
    } elseif (file_exists($tempfile)) {
        if (@copy($tempfile, $csvfile)) {
            @unlink($tempfile);
            @chmod($csvfile, 0666);
            $applied = true;
        }
    }

    if (!$applied && file_exists($tempfile)) {
        $py = '/usr/bin/python3';
        if (!is_executable($py)) {
            $py = trim((string) shell_exec('command -v python3 2>/dev/null'));
        }
        if ($py !== '' && is_executable($py)) {
            $cmd = 'sudo ' . escapeshellarg($py) . ' /sbin/piforce/renamecsv.py ' .
                escapeshellarg($tempfile) . ' ' .
                escapeshellarg($csvfile) . ' ' .
                escapeshellarg($lcdmode);
            shell_exec($cmd);
            if (!file_exists($tempfile)) {
                $applied = true;
            }
        }
    }

    if (!$applied && file_exists($tempfile)) {
        @unlink($tempfile);
    }

    if ($applied && $lcdmode === 'LCD16') {
        shell_exec('sudo service lcd-piforce restart 2>/dev/null');
    }

    return $applied;
}
