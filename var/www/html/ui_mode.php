<?php
/**
 * UI Mode Helper
 * Manages switching between Classic and Modern UI modes
 */

function get_ui_mode() {
    $mode_file = '/sbin/piforce/ui_mode.txt';
    if (file_exists($mode_file)) {
        $mode = trim(file_get_contents($mode_file));
        return ($mode === 'modern') ? 'modern' : 'classic';
    }
    return 'classic'; // Default to classic mode
}

function set_ui_mode($mode) {
    $mode_file = '/sbin/piforce/ui_mode.txt';
    $valid_modes = ['classic', 'modern'];
    
    if (in_array($mode, $valid_modes)) {
        file_put_contents($mode_file, $mode);
        return true;
    }
    return false;
}

function load_ui_styles() {
    $mode = get_ui_mode();
    
    if ($mode === 'modern') {
        // Modern UI - New design system
        echo '<link rel="stylesheet" href="css/modern-theme.css">' . "\n";
        echo '<link rel="stylesheet" href="css/components.css">' . "\n";
        echo '<link rel="stylesheet" href="css/kiosk-mode.css">' . "\n";
    } else {
        // Classic UI - Original styles
        echo '<link rel="stylesheet" href="css/sidebarstyles.css">' . "\n";
    }
}

function ui_mode_indicator() {
    $mode = get_ui_mode();
    $mode_label = ($mode === 'modern') ? 'Modern UI' : 'Classic UI';
    $mode_class = ($mode === 'modern') ? 'badge badge-primary' : 'badge badge-secondary';
    
    if ($mode === 'modern') {
        echo '<span class="' . $mode_class . '" style="margin-left: 8px;">' . $mode_label . '</span>';
    }
}
?>
