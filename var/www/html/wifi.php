<?php
include 'ui_mode.php';

include 'wifilist.php';
$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');

$error = '';

if(isset($_POST["submit"]))
{

// SECURITY: Validate all network configuration inputs
$ip = $_POST["ip"] ?? '';
$sm = $_POST["sm"] ?? '';
$gw = $_POST["gw"] ?? '';

if($ip != ''){
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Validate subnet mask
        if (!filter_var($sm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Subnet mask is invalid</b></font><br>';
        }
        // Validate gateway (if provided)
        elseif ($gw != '' && !filter_var($gw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Gateway is invalid</b></font><br>';
        }
        else {
            // SECURITY: Use escapeshellarg for each parameter
            $command = 'sudo python3 /sbin/piforce/setstatic.py wireless ' . 
                       escapeshellarg($wifimode) . ' ' . 
                       escapeshellarg($ip) . ' ' . 
                       escapeshellarg($sm) . ' ' . 
                       escapeshellarg($gw);
            shell_exec($command);
        }
    }
    else{
        $error .= '<font color="red"><b>IP Address is invalid</b></font><br>';
    }
}

 // SECURITY: Validate SSID and PSK inputs
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'] ?? '';
 }
 else
 {
  $ssid = $_POST['manssid'] ?? '';
 }
 
 // Validate SSID (max 32 characters, alphanumeric and common symbols)
 if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
     $error .= '<font color="red"><b>SSID is invalid (max 32 chars, alphanumeric only)</b></font><br>';
 }
 
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else if(strlen($_POST["psk"]) > 63)
 {
  $error .= '<font color="red"><b> Password must be 63 characters or less</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  // SECURITY: Use escapeshellarg for parameters
  $wificommand = 'sudo python3 /sbin/piforce/wificopy.py ' . 
                 escapeshellarg($ssid) . ' ' . 
                 escapeshellarg($psk);
  shell_exec($wificommand);
  $error = '<font color="green"><b>Wifi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = 'sudo python3 /sbin/piforce/reboot.py';
  shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["hotspotsubmit"]))
{
 // SECURITY: Validate SSID and PSK inputs
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'] ?? '';
 }
 else
 {
  $ssid = $_POST['manssid'] ?? '';
 }
 
 // Validate SSID
 if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
     $error .= '<font color="red"><b>SSID is invalid (max 32 chars, alphanumeric only)</b></font><br>';
 }
 
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else if(strlen($_POST["psk"]) > 63)
 {
  $error .= '<font color="red"><b> Password must be 63 characters or less</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  // SECURITY: Use escapeshellarg for parameters
  $command = 'sudo python3 /sbin/piforce/hotspotupdate.py ' . 
             escapeshellarg($ssid) . ' ' . 
             escapeshellarg($psk);
  shell_exec($command);
  $error = '<font color="green"><b>HotSpot Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = 'sudo python3 /sbin/piforce/reboot.py';
  shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["homesubmit"]))
{
 // SECURITY: Validate SSID and PSK inputs
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'] ?? '';
 }
 else
 {
  $ssid = $_POST['manssid'] ?? '';
 }
 
 // Validate SSID
 if (strlen($ssid) > 32 || !preg_match('/^[a-zA-Z0-9_\-\s\.]+$/', $ssid)) {
     $error .= '<font color="red"><b>SSID is invalid (max 32 chars, alphanumeric only)</b></font><br>';
 }
 
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else if(strlen($_POST["psk"]) > 63)
 {
  $error .= '<font color="red"><b> Password must be 63 characters or less</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  // SECURITY: Use escapeshellarg for parameters
  $command = 'sudo python3 /sbin/piforce/homeupdate.py ' . 
             escapeshellarg($ssid) . ' ' . 
             escapeshellarg($psk);
  shell_exec($command);
  $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = 'sudo python3 /sbin/piforce/reboot.py';
  shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["static"]))
{
    // SECURITY: Validate network configuration inputs
    $ip = $_POST["ip"] ?? '';
    $sm = $_POST["sm"] ?? '';
    $gw = $_POST["gw"] ?? '';

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Validate subnet mask
        if (!filter_var($sm, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Subnet mask is invalid</b></font><br>';
        }
        // Validate gateway (if provided)
        elseif ($gw != '' && !filter_var($gw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $error .= '<font color="red"><b>Gateway is invalid</b></font><br>';
        }
        elseif($error == '')
        {
            // SECURITY: Use escapeshellarg for each parameter
            $command = 'sudo python3 /sbin/piforce/setstatic.py wireless ' . 
                       escapeshellarg($wifimode) . ' ' . 
                       escapeshellarg($ip) . ' ' . 
                       escapeshellarg($sm) . ' ' . 
                       escapeshellarg($gw);
            shell_exec($command);
            $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
            $rebootcommand = 'sudo python3 /sbin/piforce/reboot.py';
            shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
            $ssid = '';
            $psk = '';
        }
    }
    else{
        $error .= '<font color="red"><b>IP Address is invalid</b></font><br>';
    }
}

if(isset($_POST["wifidhcp"]))
{
    if($error == '')
    {
        // SECURITY: No user input, static command only
        $command = 'sudo python3 /sbin/piforce/setdhcp.py wireless &';
        shell_exec($command);
        $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
        $rebootcommand = 'sudo python3 /sbin/piforce/reboot.py';
        shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
    }
}

if(isset($_POST["hotspotrestore"]))
{
    if($error == '')
    {
        // SECURITY: No user input, static command only
        $command = 'sudo python3 /sbin/piforce/hotspotwifi.py &';
        shell_exec($command);
        $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
        $rebootcommand = 'sudo python3 /sbin/piforce/reboot.py';
        shell_exec($rebootcommand . ' > /dev/null 2>/dev/null &');
    }
}


$wiredip = `ip -o -f inet addr show | awk '/eth0/ {print $4}'`;
$wirelessip = `ip -o -f inet addr show | awk '/wlan0/ {print $4}'`;
$wiredstatus =  `ip -o -f inet addr show | awk '/eth0/ {print $9}'`;
$wirelessstatus = `ip -o -f inet addr show | awk '/wlan0/ {print $9}'`;
$ssid = `iwgetid -r`;
if ($wiredstatus == "dynamic\n"){$wiredtype = "DHCP";}else{$wiredtype = "Static";}
if ($wirelessstatus == "dynamic\n"){$wirelesstype = "DHCP";}else{$wirelesstype = "Static";}

    // ── SVG icon helpers ─────────────────────────────────────────────────────
    $icon_wifi    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><circle cx="12" cy="20" r="1" fill="currentColor"/></svg>';
    $icon_eth     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6" y2="6"/><line x1="6" y1="18" x2="6" y2="18"/><line x1="12" y1="10" x2="12" y2="14"/></svg>';
    $icon_lock    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
    $icon_eye     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    $icon_eye_off = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
    $icon_warn    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
    $icon_check   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
    $icon_hotspot = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>';
    $icon_ip      = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M8 12h8M12 8v8"/></svg>';
    $icon_restore = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.37"/></svg>';

    // ── Build SSID option list ────────────────────────────────────────────────
    $ssid_options = '';
    for ($i = 1; $i <= $ssids; $i++) {
        $n = htmlspecialchars(${'name'.$i}, ENT_QUOTES, 'UTF-8');
        $ssid_options .= '<option value="'.$n.'">'.$n.'</option>';
    }

    // ── Alert ────────────────────────────────────────────────────────────────
    $alert_html = '';
    if ($error) {
        $is_success = (strpos($error, 'green') !== false);
        $alert_type = $is_success ? 'success' : 'error';
        $alert_icon = $is_success ? $icon_check : $icon_warn;
        $alert_html = '<div class="wf-alert wf-alert--'.$alert_type.'" role="alert" id="wfAlert">'
            . $alert_icon
            . '<span>' . htmlspecialchars(strip_tags($error), ENT_QUOTES, 'UTF-8') . '</span>'
            . '</div>';
    }

    // ── Page open ────────────────────────────────────────────────────────────
    echo '<html lang="en"><head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';
    echo '<title>WiPi Netbooter — WiFi Setup</title>';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '<style>
/* ── WiFi page layout ─────────────────────────────────────────────────── */
.wf-wrap { max-width: 860px; margin: 0 auto; padding: var(--space-6); }

/* header */
.wf-header { display: flex; align-items: center; gap: var(--space-4); flex-wrap: wrap; margin-bottom: var(--space-6); }
.wf-header h1 { font-size: var(--font-size-3xl); font-weight: var(--font-weight-bold); color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: var(--space-3); }
.wf-mode-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; border-radius: 999px; font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); margin-left: auto; }
.wf-mode-pill--home    { background: color-mix(in srgb, var(--color-success) 14%, transparent); color: var(--color-success); border: 1px solid color-mix(in srgb, var(--color-success) 28%, transparent); }
.wf-mode-pill--hotspot { background: color-mix(in srgb, var(--color-warning) 14%, transparent); color: var(--color-warning); border: 1px solid color-mix(in srgb, var(--color-warning) 28%, transparent); }
.wf-mode-pill__dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: wf-pulse 2s ease-in-out infinite; }
@keyframes wf-pulse { 0%,100%{opacity:1}50%{opacity:.35} }

/* alert */
.wf-alert { display: flex; align-items: flex-start; gap: var(--space-3); padding: var(--space-4) var(--space-5); border-radius: 10px; margin-bottom: var(--space-5); font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); }
.wf-alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
.wf-alert--success { background: color-mix(in srgb, var(--color-success) 12%, transparent); color: var(--color-success); border: 1px solid color-mix(in srgb, var(--color-success) 25%, transparent); }
.wf-alert--error   { background: color-mix(in srgb, var(--color-error)   10%, transparent); color: var(--color-error);   border: 1px solid color-mix(in srgb, var(--color-error)   22%, transparent); }

/* status grid */
.wf-status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); margin-bottom: var(--space-6); }
@media (max-width: 580px) { .wf-status-grid { grid-template-columns: 1fr; } }
.wf-status-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 12px; padding: var(--space-5); display: flex; gap: var(--space-4); }
.wf-status-card__icon { width: 44px; height: 44px; border-radius: 10px; background: color-mix(in srgb, var(--color-primary) 10%, transparent); color: var(--color-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.wf-status-card__icon svg { width: 21px; height: 21px; }
.wf-status-card__body { min-width: 0; }
.wf-status-card__label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--color-text-tertiary); margin-bottom: 3px; }
.wf-status-card__ip { font-family: monospace; font-size: var(--font-size-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 6px; overflow-wrap: break-word; }
.wf-status-card__tags { display: flex; flex-wrap: wrap; gap: 5px; }
.wf-status-card__ssid { font-size: var(--font-size-xs); color: var(--color-text-secondary); margin-top: 5px; }

/* tabs */
.wf-tabs { display: flex; border-bottom: 2px solid var(--color-border); margin-bottom: var(--space-6); gap: 2px; }
.wf-tab { background: none; border: none; border-bottom: 2px solid transparent; margin-bottom: -2px; padding: 10px 18px; font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); color: var(--color-text-secondary); cursor: pointer; border-radius: 6px 6px 0 0; transition: color 150ms, background 150ms, border-color 150ms; }
.wf-tab:hover { color: var(--color-text-primary); background: var(--color-surface-hover); }
.wf-tab.active { color: var(--color-primary); border-bottom-color: var(--color-primary); font-weight: 600; }
.wf-tab:focus-visible { outline: 2px solid var(--color-primary); outline-offset: 2px; }
.wf-panel { display: none; }
.wf-panel.active { display: block; }

/* form fields */
.wf-field { margin-bottom: var(--space-5); }
.wf-field label { display: block; font-size: var(--font-size-sm); font-weight: 500; color: var(--color-text-secondary); margin-bottom: 6px; }
.wf-field input, .wf-field select { width: 100%; box-sizing: border-box; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: 8px; background: var(--color-background); color: var(--color-text-primary); font-size: var(--font-size-base); transition: border-color 150ms, box-shadow 150ms; }
.wf-field input:focus, .wf-field select:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 14%, transparent); }
.wf-field__hint { font-size: var(--font-size-xs); color: var(--color-text-tertiary); margin-top: 4px; }
.wf-field__pw { position: relative; }
.wf-field__pw input { padding-right: 46px; }
.wf-field__eye { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-tertiary); display: flex; align-items: center; padding: 4px; transition: color 150ms; }
.wf-field__eye:hover { color: var(--color-text-primary); }
.wf-field__eye svg { width: 17px; height: 17px; }

/* divider label */
.wf-divider { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--color-text-tertiary); display: flex; align-items: center; gap: var(--space-3); margin: var(--space-6) 0 var(--space-4); }
.wf-divider::after { content:""; flex:1; height:1px; background: var(--color-border); }

/* actions */
.wf-actions { margin-top: var(--space-6); display: flex; gap: var(--space-3); align-items: center; flex-wrap: wrap; }
.btn[disabled], .btn.loading { pointer-events: none; opacity: .75; }
.btn.loading::after { content:""; display:inline-block; width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:wf-spin .6s linear infinite; margin-left:8px; vertical-align:middle; }
@keyframes wf-spin { to{transform:rotate(360deg)} }

/* danger card */
.wf-danger { border: 1px solid color-mix(in srgb, var(--color-error) 30%, transparent); border-radius: 12px; overflow: hidden; }
.wf-danger__head { background: color-mix(in srgb, var(--color-error) 8%, transparent); padding: var(--space-4) var(--space-5); font-size: var(--font-size-sm); font-weight: 600; color: var(--color-error); display: flex; align-items: center; gap: 8px; }
.wf-danger__head svg { width: 16px; height: 16px; }
.wf-danger__body { padding: var(--space-5); }
.wf-danger__desc { font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-4); line-height: 1.6; }

@media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
</style>';
    echo '</head><body>';

    echo modern_sliding_sidebar_nav('network');

    echo '<div class="wf-wrap">';

    // ── Header ────────────────────────────────────────────────────────────────
    echo '<div class="wf-header">';
    echo '<h1>' . arcade_icon('network') . ' WiFi Setup</h1>';
    if ($wifimode == 'hotspot') {
        echo '<span class="wf-mode-pill wf-mode-pill--hotspot"><span class="wf-mode-pill__dot"></span>HotSpot Mode</span>';
    } else {
        echo '<span class="wf-mode-pill wf-mode-pill--home"><span class="wf-mode-pill__dot"></span>Home WiFi</span>';
    }
    echo '</div>';

    // ── Alert ─────────────────────────────────────────────────────────────────
    echo $alert_html;

    // ── Status grid ───────────────────────────────────────────────────────────
    echo '<div class="wf-status-grid">';

    // Wireless card
    echo '<div class="wf-status-card">';
    echo '<div class="wf-status-card__icon">' . $icon_wifi . '</div>';
    echo '<div class="wf-status-card__body">';
    echo '<div class="wf-status-card__label">Wireless</div>';
    echo '<div class="wf-status-card__ip">' . htmlspecialchars(trim($wirelessip) ?: '—', ENT_QUOTES, 'UTF-8') . '</div>';
    echo '<div class="wf-status-card__tags">';
    echo '<span class="badge badge-primary">' . htmlspecialchars($wirelesstype, ENT_QUOTES, 'UTF-8') . '</span>';
    if ($wifimode == 'hotspot') {
        echo '<span class="badge badge-warning">HotSpot</span>';
    } else {
        echo '<span class="badge badge-success">Home</span>';
    }
    echo '</div>';
    if ($wifimode != 'hotspot' && !empty(trim($ssid))) {
        echo '<div class="wf-status-card__ssid">' . htmlspecialchars(trim($ssid), ENT_QUOTES, 'UTF-8') . '</div>';
    }
    echo '</div></div>';

    // Wired card
    echo '<div class="wf-status-card">';
    echo '<div class="wf-status-card__icon">' . $icon_eth . '</div>';
    echo '<div class="wf-status-card__body">';
    echo '<div class="wf-status-card__label">Wired (eth0)</div>';
    echo '<div class="wf-status-card__ip">' . htmlspecialchars(trim($wiredip) ?: '—', ENT_QUOTES, 'UTF-8') . '</div>';
    echo '<div class="wf-status-card__tags">';
    echo '<span class="badge badge-primary">' . htmlspecialchars($wiredtype, ENT_QUOTES, 'UTF-8') . '</span>';
    echo '</div>';
    echo '</div></div>';
    echo '</div>'; // .wf-status-grid

    // ── Tabs ──────────────────────────────────────────────────────────────────
    if ($wifimode == 'hotspot') {
        echo '<div class="wf-tabs" role="tablist">';
        echo '<button class="wf-tab active" role="tab" aria-selected="true"  aria-controls="panel-connect"  id="tab-connect"  onclick="wfTab(this,\'panel-connect\')">Connect to Home WiFi</button>';
        echo '<button class="wf-tab"        role="tab" aria-selected="false" aria-controls="panel-hotspot"  id="tab-hotspot"  onclick="wfTab(this,\'panel-hotspot\')" >HotSpot Settings</button>';
        echo '</div>';
    } else {
        echo '<div class="wf-tabs" role="tablist">';
        echo '<button class="wf-tab active" role="tab" aria-selected="true"  aria-controls="panel-wifi"     id="tab-wifi"    onclick="wfTab(this,\'panel-wifi\')"   >Update WiFi</button>';
        echo '<button class="wf-tab"        role="tab" aria-selected="false" aria-controls="panel-ip"       id="tab-ip"      onclick="wfTab(this,\'panel-ip\')"     >IP Config</button>';
        echo '<button class="wf-tab"        role="tab" aria-selected="false" aria-controls="panel-advanced" id="tab-advanced" onclick="wfTab(this,\'panel-advanced\')">Advanced</button>';
        echo '</div>';
    }

    // ── Panel: Connect to Home WiFi (hotspot mode) ────────────────────────────
    if ($wifimode == 'hotspot') {
        echo '<div class="wf-panel active" id="panel-connect" role="tabpanel" aria-labelledby="tab-connect">';
        echo '<div class="card"><div class="card-body">';
        echo '<form method="post" onsubmit="wfSubmit(this)">';

        echo '<div class="wf-field"><label for="ssid-sel-c">Detected Networks</label>';
        echo '<select id="ssid-sel-c" name="ssid" class="form-input">' . $ssid_options . '</select></div>';

        echo '<div class="wf-field"><label for="manssid-c">Or enter SSID manually</label>';
        echo '<input id="manssid-c" type="text" name="manssid" class="form-input" placeholder="Leave blank to use selection above" autocomplete="off"></div>';

        echo '<div class="wf-field"><label for="psk-c">Password</label>';
        echo '<div class="wf-field__pw"><input id="psk-c" type="password" name="psk" class="form-input" required autocomplete="current-password">';
        echo '<button type="button" class="wf-field__eye" aria-label="Toggle password visibility" onclick="wfTogglePw(\'psk-c\',this)">' . $icon_eye . '</button></div></div>';

        echo '<div class="wf-divider">Optional Static IP</div>';

        echo '<div class="wf-field"><label for="ip-c">IP Address</label>';
        echo '<input id="ip-c" type="text" name="ip" class="form-input" placeholder="Leave blank for DHCP (e.g. 192.168.1.100)" autocomplete="off">';
        echo '<div class="wf-field__hint">Leave blank to use DHCP</div></div>';

        echo '<div class="wf-field"><label for="sm-c">Subnet Mask</label>';
        echo '<input id="sm-c" type="text" name="sm" class="form-input" placeholder="e.g. 255.255.255.0" autocomplete="off"></div>';

        echo '<div class="wf-field"><label for="gw-c">Gateway</label>';
        echo '<input id="gw-c" type="text" name="gw" class="form-input" placeholder="e.g. 192.168.1.1" autocomplete="off"></div>';

        echo '<div class="wf-actions"><button type="submit" name="submit" class="btn btn-primary">Apply &amp; Reboot</button></div>';
        echo '</form></div></div>';
        echo '</div>'; // panel-connect

        // ── Panel: HotSpot Settings ───────────────────────────────────────────
        echo '<div class="wf-panel" id="panel-hotspot" role="tabpanel" aria-labelledby="tab-hotspot">';
        echo '<div class="card"><div class="card-body">';
        echo '<p style="font-size:var(--font-size-sm);color:var(--color-text-secondary);margin-bottom:var(--space-5);">Change the SSID and password for your HotSpot.</p>';
        echo '<form method="post" onsubmit="wfSubmit(this)">';

        echo '<div class="wf-field"><label for="ssid-hs">HotSpot SSID</label>';
        echo '<input id="ssid-hs" type="text" name="ssid" class="form-input" placeholder="e.g. WiPiNetbooter" autocomplete="off"></div>';

        echo '<div class="wf-field"><label for="psk-hs">New Password</label>';
        echo '<div class="wf-field__pw"><input id="psk-hs" type="password" name="psk" class="form-input" autocomplete="new-password">';
        echo '<button type="button" class="wf-field__eye" aria-label="Toggle password visibility" onclick="wfTogglePw(\'psk-hs\',this)">' . $icon_eye . '</button></div>';
        echo '<div class="wf-field__hint">Minimum 8 characters, maximum 63.</div></div>';

        echo '<div class="wf-actions"><button type="submit" name="hotspotsubmit" class="btn btn-primary">Apply &amp; Reboot</button></div>';
        echo '</form></div></div>';
        echo '</div>'; // panel-hotspot

    } else {
        // ── Panel: Update Home WiFi ───────────────────────────────────────────
        echo '<div class="wf-panel active" id="panel-wifi" role="tabpanel" aria-labelledby="tab-wifi">';
        echo '<div class="card"><div class="card-body">';
        echo '<form method="post" onsubmit="wfSubmit(this)">';

        echo '<div class="wf-field"><label for="ssid-sel-h">Detected Networks</label>';
        echo '<select id="ssid-sel-h" name="ssid" class="form-input">' . $ssid_options . '</select></div>';

        echo '<div class="wf-field"><label for="manssid-h">Or enter SSID manually</label>';
        echo '<input id="manssid-h" type="text" name="manssid" class="form-input" placeholder="Leave blank to use selection above" autocomplete="off"></div>';

        echo '<div class="wf-field"><label for="psk-h">Password</label>';
        echo '<div class="wf-field__pw"><input id="psk-h" type="password" name="psk" class="form-input" required autocomplete="current-password">';
        echo '<button type="button" class="wf-field__eye" aria-label="Toggle password visibility" onclick="wfTogglePw(\'psk-h\',this)">' . $icon_eye . '</button></div></div>';

        echo '<div class="wf-actions">';
        echo '<button type="button" class="btn btn-primary" style="cursor:pointer" onclick="wfApplyWifi(this.closest(\'form\'),this)">Apply</button>';
        echo '<button type="button" class="btn btn-secondary" style="cursor:pointer" onclick="wfTestConn(this.closest(\'form\'),this)">Test Connection</button>';
        echo '</div>';
        echo '<div id="wf-test-result" aria-live="polite" style="margin-top:var(--space-4);"></div>';
        echo '</form></div></div>';
        echo '</div>'; // panel-wifi

        // ── Panel: IP Config ──────────────────────────────────────────────────
        echo '<div class="wf-panel" id="panel-ip" role="tabpanel" aria-labelledby="tab-ip">';
        echo '<div class="card"><div class="card-body">';
        echo '<p style="font-size:var(--font-size-sm);color:var(--color-text-secondary);margin-bottom:var(--space-5);">Set a fixed IP address for the wireless interface.</p>';
        echo '<form method="post" onsubmit="wfSubmit(this)">';

        echo '<div class="wf-field"><label for="ip-s">IP Address</label>';
        echo '<input id="ip-s" type="text" name="ip" class="form-input" placeholder="e.g. 192.168.1.100" autocomplete="off"></div>';

        echo '<div class="wf-field"><label for="sm-s">Subnet Mask</label>';
        echo '<input id="sm-s" type="text" name="sm" class="form-input" placeholder="e.g. 255.255.255.0" autocomplete="off"></div>';

        echo '<div class="wf-field"><label for="gw-s">Gateway</label>';
        echo '<input id="gw-s" type="text" name="gw" class="form-input" placeholder="e.g. 192.168.1.1" autocomplete="off"></div>';

        echo '<div class="wf-actions">';
        echo '<button type="submit" name="static" class="btn btn-primary">Apply &amp; Reboot</button>';
        if ($wirelesstype == 'Static') {
            echo '<button type="submit" name="wifidhcp" class="btn btn-secondary" formnovalidate>Switch to DHCP</button>';
        }
        echo '</div>';
        echo '</form></div></div>';
        echo '</div>'; // panel-ip

        // ── Panel: Advanced ───────────────────────────────────────────────────
        echo '<div class="wf-panel" id="panel-advanced" role="tabpanel" aria-labelledby="tab-advanced">';
        echo '<div class="wf-danger">';
        echo '<div class="wf-danger__head">' . $icon_warn . ' Restore HotSpot Mode</div>';
        echo '<div class="wf-danger__body">';
        echo '<p class="wf-danger__desc">This will switch the device back to HotSpot mode and reboot. All home WiFi settings will be cleared.</p>';
        echo '<form method="post" onsubmit="wfSubmit(this)">';
        echo '<button type="submit" name="hotspotrestore" class="btn btn-warning" onclick="return confirm(\'Restore HotSpot mode and reboot?\')">Restore HotSpot Mode</button>';
        echo '</form>';
        echo '</div></div>';
        echo '</div>'; // panel-advanced
    }

    echo '</div>'; // .wf-wrap

    echo '<script>
function toggleSidebar(){
  var s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");
  if(s)s.classList.toggle("open");
  if(o)o.classList.toggle("show");
  if(b)b.classList.toggle("open");
}
function wfTab(btn, panelId) {
  var tabs = btn.closest(".wf-tabs").querySelectorAll(".wf-tab");
  var panels = document.querySelectorAll(".wf-panel");
  tabs.forEach(function(t){ t.classList.remove("active"); t.setAttribute("aria-selected","false"); });
  panels.forEach(function(p){ p.classList.remove("active"); });
  btn.classList.add("active");
  btn.setAttribute("aria-selected","true");
  var p = document.getElementById(panelId);
  if (p) p.classList.add("active");
}
function wfTogglePw(inputId, btn) {
  var inp = document.getElementById(inputId);
  if (!inp) return;
  var show = inp.type === "password";
  inp.type = show ? "text" : "password";
  btn.innerHTML = show
    ? ' . json_encode($icon_eye_off) . '
    : ' . json_encode($icon_eye) . ';
}
function wfSubmit(form) {
  var btn = form.querySelector("button[type=submit]");
  if (btn) { btn.classList.add("loading"); btn.disabled = true; }
}
function wfApplyWifi(form, btn) {
  var ssid    = form.querySelector("[name=ssid]")    ? form.querySelector("[name=ssid]").value    : "";
  var manssid = form.querySelector("[name=manssid]") ? form.querySelector("[name=manssid]").value : "";
  var psk     = form.querySelector("[name=psk]")     ? form.querySelector("[name=psk]").value     : "";
  var result  = document.getElementById("wf-test-result");
  if (!psk) { if(result){result.innerHTML="<div class=\"wf-alert wf-alert--error\">Enter a password first.</div>";} return; }
  btn.disabled = true; btn.classList.add("loading");
  if (result) result.innerHTML = "<div class=\"wf-alert\" style=\"background:var(--color-surface);border:1px solid var(--color-border);color:var(--color-text-secondary);\">Applying&hellip; please wait (~15 s)</div>";
  var fd = new FormData();
  fd.append("ssid",    ssid);
  fd.append("manssid", manssid);
  fd.append("psk",     psk);
  fetch("testwifi.php", {method:"POST", body:fd})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (result) {
        if (d.success) {
          result.innerHTML = "<div class=\"wf-alert wf-alert--success\"><svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"width:18px;height:18px;flex-shrink:0\"><polyline points=\"20 6 9 17 4 12\"/></svg><span>Connected &amp; saved — <strong>" + d.ssid + "</strong>" + (d.ip ? " &mdash; " + d.ip : "") + "</span></div>";
          // Update the status card IP and SSID live
          var ipEl = document.querySelector(".wf-status-card__ip");
          if (ipEl && d.ip) ipEl.textContent = d.ip;
          var ssidEl = document.querySelector(".wf-status-card__ssid");
          if (ssidEl) ssidEl.textContent = d.ssid;
        } else {
          result.innerHTML = "<div class=\"wf-alert wf-alert--error\"><svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"width:18px;height:18px;flex-shrink:0\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"15\" y1=\"9\" x2=\"9\" y2=\"15\"/><line x1=\"9\" y1=\"9\" x2=\"15\" y2=\"15\"/></svg><span>" + (d.error ? d.error : "Could not connect — check SSID and password.") + "</span></div>";
        }
      }
    })
    .catch(function(){
      if (result) result.innerHTML = "<div class=\"wf-alert wf-alert--error\">Request failed — check your connection.</div>";
    })
    .finally(function(){ btn.disabled = false; btn.classList.remove("loading"); });
}
function wfTestConn(form, btn) {
  var ssid    = form.querySelector("[name=ssid]")    ? form.querySelector("[name=ssid]").value    : "";
  var manssid = form.querySelector("[name=manssid]") ? form.querySelector("[name=manssid]").value : "";
  var psk     = form.querySelector("[name=psk]")     ? form.querySelector("[name=psk]").value     : "";
  var result  = document.getElementById("wf-test-result");
  if (!psk) { if(result){result.innerHTML="<div class=\"wf-alert wf-alert--error\">Enter a password first.</div>";} return; }
  btn.disabled = true; btn.classList.add("loading");
  if (result) result.innerHTML = "<div class=\"wf-alert\" style=\"background:var(--color-surface);border:1px solid var(--color-border);color:var(--color-text-secondary);\">Testing connection — please wait (~10 s)&hellip;</div>";
  var fd = new FormData();
  fd.append("ssid",    ssid);
  fd.append("manssid", manssid);
  fd.append("psk",     psk);
  fetch("testwifi.php", {method:"POST", body:fd})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (result) {
        if (d.success) {
          result.innerHTML = "<div class=\"wf-alert wf-alert--success\"><svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"width:18px;height:18px;flex-shrink:0\"><polyline points=\"20 6 9 17 4 12\"/></svg><span>Connected to <strong>" + d.ssid + "</strong>" + (d.ip ? " &mdash; " + d.ip : "") + "</span></div>";
        } else {
          result.innerHTML = "<div class=\"wf-alert wf-alert--error\"><svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"width:18px;height:18px;flex-shrink:0\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"15\" y1=\"9\" x2=\"9\" y2=\"15\"/><line x1=\"9\" y1=\"9\" x2=\"15\" y2=\"15\"/></svg><span>" + (d.error ? d.error : "Could not connect — check SSID and password.") + "</span></div>";
        }
      }
    })
    .catch(function(){
      if (result) result.innerHTML = "<div class=\"wf-alert wf-alert--error\">Request failed — check your connection.</div>";
    })
    .finally(function(){ btn.disabled = false; btn.classList.remove("loading"); });
}
// Auto-dismiss alert after 6 s
(function(){ var a=document.getElementById("wfAlert"); if(a){ setTimeout(function(){ a.style.transition="opacity .5s"; a.style.opacity="0"; setTimeout(function(){ a.remove(); },500); },6000); } })();
</script>';
    echo '</body></html>';
?>
