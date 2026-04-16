<?php
include 'auditscanresults.php';
$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$csvfile = 'csv/romsinfo.csv';
$rompath = '/boot/roms/';
$tempfile = tempnam('.', 'tmp');

header('Refresh: 4; url=gamelist.php?display=all');
include_once 'ui_mode.php';

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - ROM Audit Save</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
load_ui_styles();
echo '</head><body class="kiosk-mode">';
echo modern_sliding_sidebar_nav('games');
echo '<div class="container p-6" style="text-align: center; padding-top: 80px;">';
echo '<h1 class="text-2xl" style="margin-bottom: 16px;">' . arcade_icon('edit') . ' Updating game list</h1>';
echo '<p style="color: var(--color-text-secondary); margin-bottom: 24px;">Applying ROM audit results to your game list…</p>';
echo '<div class="spinner" style="margin: 0 auto; width: 48px; height: 48px; border: 4px solid var(--color-border); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>';
echo '<style>@keyframes spin { to { transform: rotate(360deg); } }</style>';
echo '<p style="margin-top: 24px; font-size: var(--font-size-sm); color: var(--color-text-muted);">Redirecting to the game list…</p>';
echo '</div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
echo '</body></html>';

if (!$input = fopen($csvfile, 'r')) {
    die('could not open existing csv file');
}
if (!$output = fopen($tempfile, 'w')) {
    die('could not open temporary output file');
}

if ($_GET['rename'] ?? '' === 'yes') {
    for ($x = 1; $x <= $successes; $x++) {
        $oldname = $rompath . basename(${'filename'.$x});
        $newname = $rompath . basename(${'auditname'.$x});

        $renamecmd = 'sudo python3 /sbin/piforce/renamecsv.py ' .
               escapeshellarg($oldname) . ' ' .
               escapeshellarg($newname) . ' ' .
               escapeshellarg($lcdmode);
        shell_exec($renamecmd . ' > /dev/null 2>/dev/null &');
    }

    $i = 1;
    while (($data = fgetcsv($input)) !== false) {
        $namecheck = ${'auditname'.$i};
        $filename = ${'filename'.$i};
        if ($data[17] == $namecheck) {
            $data[1] = $namecheck;
            if ($i < $successes) {
                $i++;
            }
        }
        fputcsv($output, $data);
    }

    fflush($input);
    fflush($output);
    fclose($input);
    fclose($output);

    $command = 'sudo python3 /sbin/piforce/renamecsv.py ' .
           escapeshellarg($tempfile) . ' ' .
           escapeshellarg($csvfile) . ' ' .
           escapeshellarg($lcdmode);
    shell_exec($command);
} else {
    $i = 1;
    while (($data = fgetcsv($input)) !== false) {
        $namecheck = ${'auditname'.$i};
        $filename = ${'filename'.$i};
        if ($data[17] == $namecheck) {
            $data[1] = $filename;
            if ($i < $successes) {
                $i++;
            }
        }
        fputcsv($output, $data);
    }

    fflush($input);
    fflush($output);
    fclose($input);
    fclose($output);

    $command = 'sudo python3 /sbin/piforce/renamecsv.py ' .
           escapeshellarg($tempfile) . ' ' .
           escapeshellarg($csvfile) . ' ' .
           escapeshellarg($lcdmode);
    shell_exec($command);
}

?>
