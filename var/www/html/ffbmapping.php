<?php
include_once 'ui_mode.php';
include 'devicelist.php';

$error = '';

if (isset($_GET["command"]) && $_GET["command"] === 'delete') {
    $deletefile = $_GET["filetodelete"] ?? '';
    $filename   = basename($deletefile);
    if (strpos($deletefile, '/etc/openffb/games/') === 0 && preg_match('/^[a-zA-Z0-9_\-]+$/', $filename)) {
        shell_exec('sudo python3 /sbin/piforce/delete.py ' . escapeshellarg($deletefile));
    }
    header("Location: ffbmapping.php");
    exit;
}

if (isset($_POST["submit"])) {
    if (empty($_POST["name"]) || $_POST["name"] === 'New Mapping File') {
        $error = 'Filename is required';
    } else {
        $result = strtolower(str_replace(' ', '-', $_POST["name"]));
        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $result)) {
            $newfile = fopen('/etc/openffb/games/' . $result, "w");
            fclose($newfile);
            $error = 'success';
        } else {
            $error = 'Invalid filename characters';
        }
    }
}

shell_exec('sudo python3 /sbin/piforce/mappingfiles.py');
$mappingfiles = is_dir('/etc/openffb/games') ? scandir('/etc/openffb/games') : [];


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - FFB Mappings</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';
    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('gamepad') . ' OpenFFB Mapping Files</h1>';
    echo '<p class="page-intro">Manage force feedback mapping files for OpenFFB.</p>';

    if ($error === 'success') {
        echo '<div class="alert alert-success" style="max-width:640px;margin-bottom:16px;">Entry added successfully.</div>';
    } elseif ($error) {
        echo '<div class="alert alert-warning" style="max-width:640px;margin-bottom:16px;">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    echo '<div class="card">';
    echo '<div class="card-header"><h2 class="card-title">FFB Mapping Files</h2></div>';
    echo '<div class="card-body" style="padding:0;">';
    echo '<table class="table" style="width:100%"><thead><tr><th>Mapping File</th><th>Actions</th></tr></thead><tbody>';
    for ($i = 2; $i < count($mappingfiles); $i++) {
        $name = $mappingfiles[$i];
        $path = '/etc/openffb/games/' . $name;
        echo '<tr><td>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td><a href="editor.php?mode=ffb&mappingfile=' . urlencode($path) . '" class="btn btn-secondary btn-sm">Edit</a> ';
        echo '<a href="ffbmapping.php?command=delete&filetodelete=' . urlencode($path) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '?\')">Delete</a></td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
    echo '<div class="card-footer">';
    echo '<form method="post" style="display:flex;gap:8px;align-items:center;">';
    echo '<input type="text" name="name" placeholder="New mapping filename" class="form-input" style="max-width:280px;">';
    echo '<input type="submit" name="submit" class="btn btn-primary" value="Add File">';
    echo '</form>';
    echo '</div></div>';

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';

?>
