<?php
// SECURITY: Static command with no user input
$command = 'sudo python3 /sbin/piforce/devicelist.py';
shell_exec($command);

include_once 'ui_mode.php';
include 'devicelist.php';

// Handle commands before any output
if (isset($_GET["command"])) {
    if ($_GET["command"] == 'rescan') {
        header("Location: devicescan.php");
        exit;
    }

    if ($_GET["command"] == 'enable') {
        $enablefile = basename($_GET["file"] ?? '');
        if (preg_match('/^[a-zA-Z0-9_\-\.]+\.disabled$/', $enablefile)) {
            $without_extension = substr($enablefile, 0, strrpos($enablefile, "."));
            shell_exec('sudo python3 /sbin/piforce/renamecsv.py ' . escapeshellarg($enablefile) . ' ' . escapeshellarg($without_extension));
        }
        header("Location: devicescan.php");
        exit;
    }

    if ($_GET["command"] == 'disable') {
        $disablefile = basename($_GET["file"] ?? '');
        if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $disablefile)) {
            shell_exec('sudo python3 /sbin/piforce/renamecsv.py ' . escapeshellarg($disablefile) . ' ' . escapeshellarg($disablefile . '.disabled'));
        }
        header("Location: devicescan.php");
        exit;
    }

    if ($_GET["command"] == 'delete') {
        $deletefile = basename($_GET["file"] ?? '');
        if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $deletefile)) {
            shell_exec('sudo python3 /sbin/piforce/delete.py ' . escapeshellarg($deletefile));
        }
        header("Location: devicescan.php");
        exit;
    }
}


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Device Scan</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';

    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('scan') . ' OpenJVS Device Scan</h1>';
    echo '<p class="page-intro">Found <strong>' . intval($devices) . '</strong> device(s). Select a device to configure it for OpenJVS.</p>';

    echo '<div style="margin-bottom: 24px;">';
    echo '<a href="devicescan.php" class="btn btn-secondary">' . arcade_icon('refresh') . ' Rescan Devices</a>';
    echo '</div>';

    if ($devices == 0) {
        echo '<div class="empty-state"><p>No devices found. Make sure your controllers are connected and try rescanning.</p></div>';
    } else {
        echo '<div class="grid grid-cols-2">';
        for ($i = 1; $i <= $devices; $i++) {
            $filename = ${'file'.$i};
            $disabledfilename = $filename . '.disabled';
            $nameEsc = htmlspecialchars(${'name'.$i}, ENT_QUOTES, 'UTF-8');
            $pathEsc = htmlspecialchars(${'path'.$i}, ENT_QUOTES, 'UTF-8');

            if (file_exists($filename)) {
                $status = 'enabled';
            } elseif (file_exists($disabledfilename)) {
                $status = 'disabled';
            } else {
                $status = 'not found';
            }

            echo '<div class="card">';
            echo '<div class="card-header"><h3 class="card-title">' . $nameEsc . '</h3></div>';
            echo '<div class="card-body">';
            echo '<p><strong>Path:</strong> ' . $pathEsc . '</p>';
            echo '<p style="margin-top: 8px;">';
            if ($status === 'enabled') {
                echo '<span class="badge badge-success">&#10003; Enabled</span>';
            } elseif ($status === 'disabled') {
                echo '<span class="badge badge-secondary">&#9675; Disabled</span>';
            } else {
                echo '<span class="badge badge-warning">Not Found</span>';
            }
            echo '</p>';
            echo '<div style="margin-top: 16px; display: flex; gap: 8px; flex-wrap: wrap;">';
            if ($status === 'disabled') {
                echo '<a href="devicescan.php?command=enable&file=' . urlencode($disabledfilename) . '" class="btn btn-primary btn-sm">&#10003; Enable</a>';
                echo '<a href="devicescan.php?command=delete&file=' . urlencode($disabledfilename) . '" class="btn btn-danger btn-sm">Delete</a>';
            } elseif ($status === 'enabled') {
                echo '<a href="devicescan.php?command=disable&file=' . urlencode($filename) . '" class="btn btn-warning btn-sm">&#9675; Disable</a>';
            } else {
                echo '<a href="deviceconfig.php?path=' . urlencode(${'path'.$i}) . '" class="btn btn-primary btn-sm">Configure</a>';
            }
            echo '</div>';
            echo '</div></div>';
        }
        echo '</div>';
    }

    echo '</div>';
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';


?>
