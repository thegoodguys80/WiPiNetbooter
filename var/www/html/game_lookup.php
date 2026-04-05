<?php
/**
 * Resolve game list artwork + title from csv/romsinfo.csv (same source as gamelist.php).
 */

function game_metadata_from_rom($rom_filename) {
    $out = ['image' => '', 'title' => '', 'system' => ''];
    $csv = __DIR__ . '/csv/romsinfo.csv';
    if (!is_readable($csv)) {
        return $out;
    }
    $f = fopen($csv, 'r');
    if ($f === false) {
        return $out;
    }
    fgetcsv($f);
    while (($row = fgetcsv($f)) !== false) {
        if (!isset($row[1]) || $row[1] !== $rom_filename) {
            continue;
        }
        $out['image'] = isset($row[2]) ? trim($row[2]) : '';
        $out['title'] = isset($row[4]) ? trim($row[4]) : '';
        $out['system'] = isset($row[0]) ? trim($row[0]) : '';
        break;
    }
    fclose($f);
    return $out;
}

function game_metadata_from_display_title($display_title) {
    $out = ['image' => '', 'title' => '', 'system' => ''];
    if ($display_title === '') {
        return $out;
    }
    $csv = __DIR__ . '/csv/romsinfo.csv';
    if (!is_readable($csv)) {
        return $out;
    }
    $f = fopen($csv, 'r');
    if ($f === false) {
        return $out;
    }
    fgetcsv($f);
    $needle = trim($display_title);
    while (($row = fgetcsv($f)) !== false) {
        if (!isset($row[4])) {
            continue;
        }
        if (trim($row[4]) === $needle) {
            $out['image'] = isset($row[2]) ? trim($row[2]) : '';
            $out['title'] = trim($row[4]);
            $out['system'] = isset($row[0]) ? trim($row[0]) : '';
            break;
        }
    }
    fclose($f);
    return $out;
}

/**
 * @return array{image: string, title: string, system: string, display_title: string, art_url: string}
 */
function game_loading_metadata($rom_filename, $query_title_fallback) {
    $meta = game_metadata_from_rom($rom_filename);
    if ($meta['title'] === '' && $meta['image'] === '' && $query_title_fallback !== '') {
        $meta = game_metadata_from_display_title($query_title_fallback);
    }
    $display_title = $meta['title'] !== '' ? $meta['title'] : $query_title_fallback;

    $art_url = '';
    $img = $meta['image'];
    if ($img !== '' && preg_match('/^[a-zA-Z0-9_\-\.]+\.(png|jpg|jpeg|gif|webp)$/i', $img)) {
        $art_url = 'images/' . $img;
    }

    return [
        'image' => $meta['image'],
        'title' => $meta['title'],
        'system' => $meta['system'],
        'display_title' => $display_title,
        'art_url' => $art_url,
    ];
}

/**
 * Hero artwork from list image only (matches gamelist images/ path). No letter fallback.
 */
function game_loading_art_block_html($art_url, $display_title) {
    if ($art_url === '') {
        return '';
    }
    $alt = htmlspecialchars($display_title, ENT_QUOTES, 'UTF-8');
    $src = htmlspecialchars($art_url, ENT_QUOTES, 'UTF-8');
    return '<div class="game-loading__art-wrap">'
        . '<img class="game-loading__art" src="' . $src . '" alt="' . $alt . '" loading="eager">'
        . '</div>';
}
