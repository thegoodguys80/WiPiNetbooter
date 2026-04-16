<?php
ini_set('zlib.output_compression', false);
while (@ob_end_flush());
ini_set('implicit_flush', true);
ob_implicit_flush(true);
set_time_limit(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

include_once 'ui_mode.php';

/**
 * IPs already stored in csv/dimms.csv (column: ipaddress).
 *
 * @return array<string, true>
 */
function dimms_csv_existing_ips() {
    $path = __DIR__ . '/csv/dimms.csv';
    $ips = [];
    if (!is_readable($path)) {
        return $ips;
    }
    $f = fopen($path, 'r');
    if ($f === false) {
        return $ips;
    }
    $header = fgetcsv($f);
    while (($row = fgetcsv($f)) !== false) {
        if (!isset($row[1])) {
            continue;
        }
        $ip = trim($row[1]);
        if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ips[$ip] = true;
        }
    }
    fclose($f);
    return $ips;
}

/**
 * Parse nmap grepable (-oG) output for hosts with TCP 10703 open.
 *
 * @return string[]
 */
function parse_nmap_grepable_for_netdimm($text) {
    $found = [];
    foreach (explode("\n", $text) as $line) {
        if (strpos($line, '10703/open') === false) {
            continue;
        }
        if (preg_match('/^Host:\s+([0-9]{1,3}(?:\.[0-9]{1,3}){3})\s/i', $line, $m)) {
            $found[$m[1]] = true;
        }
    }
    return array_keys($found);
}

/**
 * Pure-PHP TCP port scan fallback (no nmap required).
 * Iterates all .1–.254 hosts in a /24 CIDR and tries to connect on port 10703.
 *
 * @return string[] IP addresses with port 10703 open
 */
function php_scan_cidr_for_netdimm($cidr) {
    if (!preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3})\.\d{1,3}\/\d+$/', $cidr, $m)) {
        return [];
    }
    $base = $m[1];
    $found = [];
    $timeout = 1.5; // seconds per host — NetDIMMs can be slow to accept
    for ($i = 1; $i <= 254; $i++) {
        $ip = $base . '.' . $i;
        $fp = @fsockopen('tcp://' . $ip, 10703, $errno, $errstr, $timeout);
        if ($fp !== false) {
            $found[] = $ip;
            fclose($fp);
        }
        if ($i % 32 === 0) {
            echo '.';
            flush();
        }
    }
    return $found;
}

function scan_target() {
    $nmap = trim((string) shell_exec('command -v nmap 2>/dev/null'));
    $use_nmap = ($nmap !== '');

    if (!$use_nmap) {
        echo '<div class="alert alert-info" style="margin-bottom:16px;"><strong>nmap not found</strong> — using built-in PHP scanner (slower). '
           . 'For faster scans install nmap: <code>sudo apt install nmap</code></div>';
    }

    $existing = dimms_csv_existing_ips();

    // Try ip command first (Raspberry Pi), fall back to hostname -I (Docker/other)
    $ipranges = shell_exec('ip -o -f inet addr show 2>/dev/null | awk \'/scope global/ {print $4}\'') ?? '';
    $scanranges = array_filter(explode("\n", rtrim($ipranges)));

    if (empty($scanranges)) {
        $fallback = shell_exec('hostname -I 2>/dev/null') ?? '';
        foreach (array_filter(explode(' ', trim($fallback))) as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $scanranges[] = $ip . '/24';
            }
        }
    }

    if (empty($scanranges)) {
        echo '<p class="text-error"><strong>No active network interfaces found.</strong> Make sure the Pi is connected to a network.</p>';
        return;
    }

    echo '<p><strong>' . count($scanranges) . ' IP range(s) detected</strong></p>';

    foreach ($scanranges as $scanrange) {
        if (!preg_match('/^[0-9\.:\/]+$/', $scanrange)) {
            echo '<p class="text-error">Invalid IP range: ' . htmlspecialchars($scanrange, ENT_QUOTES, 'UTF-8') . '</p>';
            continue;
        }
        echo '<p>Scanning ' . htmlspecialchars($scanrange, ENT_QUOTES, 'UTF-8') . ' for NetDIMMs (port 10703)…</p>';
        flush();

        if ($use_nmap) {
            // -Pn: do not skip hosts that ignore ICMP (many NetDIMMs do not reply to ping)
            // -sT: TCP connect scan (works without root; reliable for www-data / Docker)
            // -oG -: grepable output to stdout (no write to /sbin/piforce — avoids permission issues)
            $cmd = escapeshellcmd($nmap)
                . ' -sT -Pn --open -p10703 -oG - '
                . escapeshellarg($scanrange)
                . ' 2>&1';

            echo '<pre style="background:var(--color-surface-hover,#1a1a1a);padding:12px;border-radius:6px;font-size:13px;overflow-x:auto;">';
            $out = '';
            $a = popen($cmd, 'r');
            if ($a === false) {
                echo htmlspecialchars("Failed to start nmap.\n", ENT_QUOTES, 'UTF-8');
            } else {
                while (($b = fgets($a, 8192)) !== false) {
                    $out .= $b;
                    echo htmlspecialchars($b, ENT_QUOTES, 'UTF-8');
                    flush();
                }
                pclose($a);
            }
            echo '</pre>';
            $dimms = parse_nmap_grepable_for_netdimm($out);
        } else {
            echo '<p style="color:var(--color-text-muted,#888);font-size:13px;">Probing 254 hosts (this may take ~1–2 minutes)… ';
            flush();
            $dimms = php_scan_cidr_for_netdimm($scanrange);
            echo ' done.</p>';
            flush();
        }

        if (count($dimms) === 0) {
            echo '<p class="text-error"><strong>No NetDIMMs found on this range.</strong></p>';
            continue;
        }

        foreach ($dimms as $dimm) {
            $dimm = trim($dimm);
            if ($dimm === '' || !filter_var($dimm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                continue;
            }
            if (isset($existing[$dimm])) {
                echo '<p class="text-muted"><strong>Already listed:</strong> ' . htmlspecialchars($dimm, ENT_QUOTES, 'UTF-8') . '</p>';
                continue;
            }

            echo '<p class="text-success"><strong>NetDIMM found — adding ' . htmlspecialchars($dimm, ENT_QUOTES, 'UTF-8') . '</strong></p>';

            $handle = fopen(__DIR__ . '/csv/dimms.csv', 'a');
            if ($handle === false) {
                echo '<p class="text-error">Could not write csv/dimms.csv (check permissions).</p>';
                continue;
            }
            if (flock($handle, LOCK_EX)) {
                fputcsv($handle, ['Netdimm', $dimm, 'Sega Naomi']);
                flock($handle, LOCK_UN);
            }
            fclose($handle);

            $existing[$dimm] = true;
        }
    }
}

/**
 * Handle manual add-by-IP form submission.
 * Returns a status message string or empty string.
 */
function handle_manual_add() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['manual_ip'])) {
        return '';
    }
    $ip = trim($_POST['manual_ip']);
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return '<p class="text-error"><strong>Invalid IP address:</strong> ' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    $existing = dimms_csv_existing_ips();
    if (isset($existing[$ip])) {
        return '<p class="text-muted"><strong>' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '</strong> is already in your DIMM list.</p>';
    }
    $handle = fopen(__DIR__ . '/csv/dimms.csv', 'a');
    if ($handle === false) {
        return '<p class="text-error">Could not write csv/dimms.csv (check permissions).</p>';
    }
    if (flock($handle, LOCK_EX)) {
        fputcsv($handle, ['Netdimm', $ip, 'Sega Naomi']);
        flock($handle, LOCK_UN);
    }
    fclose($handle);
    return '<p class="text-success"><strong>' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '</strong> added to your DIMM list. <a href="dimms.php">Manage NetDIMMs →</a></p>';
}

$manual_add_msg = handle_manual_add();

// --- Page output ---


    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - NetDIMM Scanner</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';

    echo modern_sliding_sidebar_nav('netdimms');
    echo '<div class="container p-6">';
    echo '<h1>' . arcade_icon('scan') . ' NetDIMM Scanner</h1>';
    echo '<p class="page-intro">Scans all active network interfaces for NetDIMM boards on port 10703 and adds any found to your DIMM list automatically.</p>';

    // Manual-add card (always visible)
    echo '<div class="card" style="max-width:560px;margin-bottom:24px;">';
    echo '<div class="card-header"><h2 class="card-title">' . arcade_icon('netdimms') . ' Add NetDIMM by IP</h2></div>';
    echo '<div class="card-body">';
    if ($manual_add_msg !== '') {
        echo $manual_add_msg;
    }
    echo '<form method="post" action="dimmscanner.php" style="display:flex;gap:8px;align-items:flex-end;margin-top:8px;">';
    echo '<div style="flex:1;"><label style="display:block;margin-bottom:4px;font-size:13px;">IP Address</label>';
    echo '<input type="text" name="manual_ip" placeholder="e.g. 192.168.1.40" class="form-control" style="width:100%;" required></div>';
    echo '<button type="submit" class="btn btn-primary">Add</button>';
    echo '</form>';
    echo '</div></div>';

    if (isset($_GET['scan']) && $_GET['scan'] === 'start') {
        echo '<div class="card" style="margin-bottom: 24px;">';
        echo '<div class="card-header"><h2 class="card-title">' . arcade_icon('lightning') . ' Scan Results</h2></div>';
        echo '<div class="card-body">';
        scan_target();
        echo '</div></div>';
        echo '<a href="dimms.php" class="btn btn-primary">' . arcade_icon('netdimms') . ' Go to NetDIMM Manager</a>';
    } else {
        echo '<div class="card" style="max-width: 560px;">';
        echo '<div class="card-header"><h2 class="card-title">' . arcade_icon('scan') . ' Auto Scan</h2></div>';
        echo '<div class="card-body">';
        echo '<p>Scans your wired and wireless networks for NetDIMM boards on port 10703 and adds any found automatically.</p>';
        echo '<div class="alert alert-info" style="margin-top: 16px;">';
        echo '<strong>Note:</strong> The scan may take 1–3 minutes per network range. Hosts that do not respond to ping are still checked.';
        echo '</div>';
        echo '</div>';
        echo '<div class="card-footer">';
        echo '<a href="dimmscanner.php?scan=start" class="btn btn-primary">' . arcade_icon('lightning') . ' Start Scan</a>';
        echo '<a href="dimms.php" class="btn btn-secondary" style="margin-left: 8px;">Cancel</a>';
        echo '</div></div>';
    }

    echo '</div>'; // container
    echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
    echo '</body></html>';


?>
