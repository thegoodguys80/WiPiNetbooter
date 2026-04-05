<?php

include_once 'ui_mode.php';

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="NetDIMM Management">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';

load_ui_styles();

function pinger($address){
        if (!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }
        // TCP connect to port 10703.
        // - Success          → NetDIMM is online and ready to receive a game.
        // - ECONNREFUSED(111)→ Device is online but port is closed (game running).
        // - Timeout          → Device is not reachable at all.
        @fsockopen('tcp://' . $address, 10703, $errno, $errstr, 2.0);
        if ($errno === 0 || $errno === 111) {
            return true; // connected or refused — device is present on the network
        }
        return false;
    }

$error = '';
$name = '';
$ipaddress = '';
$type = '';

if(isset($_POST["submit"]))
{
 if(empty($_POST["name"]))
 {
  $error .= '<label class="offline">*Name is required* </label>';
 }
 else
 {
  $name = $_POST["name"];
 }
 if(empty($_POST["ipaddress"]))
 {
  $error .= '<label class="offline">*IP Address is required*</label>';
 }
 else
 {
  $ipaddress = $_POST["ipaddress"];
 }
 if(empty($_POST["type"]))
 {
  $error .= '<label class="offline">Type is required</label>';
 }
 else
 {
  $type = $_POST["type"];
 }

 if($error == '')
 {
  $file_open = fopen("csv/dimms.csv", "a");
  $form_data = array(
   'name'  => $name,
   'ipaddress' => $ipaddress,
   'type' => $type
  );
  fputcsv($file_open, $form_data);
  echo "<meta http-equiv='refresh' content='1'>";
  $error = '<label class="online">Entry Added Successfully</label>';
  $name = '';
  $ipaddress = '';
  $type = '';
 }
}

?>

<!DOCTYPE html>
<html>
<body>

<?php

echo modern_sliding_sidebar_nav('netdimms');
echo '<div class="container p-6">';
echo '<header class="dimms-page__toolbar" role="banner">';
echo '<h1 class="text-3xl">'.arcade_icon('netdimms').' NetDIMM Management</h1>';
echo '<div class="dimms-page__actions">';
echo '<a href="dimmscanner.php" class="btn btn-primary">'.arcade_icon('scan').' Scan Network</a>';
echo '</div>';
echo '</header>';
$f = fopen("csv/dimms.csv", "r");
$headers = ($row = fgetcsv($f));
$i = 1;
$row = fgetcsv($f);

if ($i == 1 && ($row === false || $row[1] == null)){
    echo '<div class="empty-state">';
    echo '<span class="empty-state__icon arcade-icon arcade-icon--netdimms" aria-hidden="true"></span>';
    echo '<h2>No NetDIMMs Configured</h2>';
    echo '<p>Add NetDIMMs manually below or scan your network to find them automatically.</p>';
    echo '<a href="dimmscanner.php" class="btn btn-primary">'.arcade_icon('scan').' Scan Network</a>';
    echo '</div>';
} else {
rewind($f);
$headers = ($row = fgetcsv($f));

echo '<div class="grid grid-cols-3" style="margin-bottom: 32px;">';

while (($row = fgetcsv($f)) !== false) {
    $isOnline = pinger($row[1]);

        echo '<div class="card">';
        echo '<form action="updatedimms.php" method="get">';
        echo '<input type="hidden" name="ip" value="'.$row[1].'">';
        echo '<input type="hidden" name="action" value="update">';
        echo '<input type="hidden" name="linenum" value="'.$i.'">';
        
        echo '<div class="card-header">';
        echo '<div class="flex" style="justify-content: space-between; align-items: center;">';
        echo '<input type="text" name="name" class="form-input" value="'.$row[0].'" style="font-size: 18px; font-weight: 600; border: 1px solid var(--color-border); padding: 8px;" />';
        if ($isOnline) {
            echo '<span class="badge badge-success">● Online</span>';
        } else {
            echo '<span class="badge badge-error">● Offline</span>';
        }
        echo '</div></div>';
        
        echo '<div class="card-body">';
        echo '<div style="margin-bottom: 16px;">';
        echo '<label class="form-label"><strong>IP Address</strong></label>';
        echo '<div style="font-size: 16px; color: var(--color-text-primary); padding: 8px 0;">'.$row[1].'</div>';
        echo '</div>';
        
        echo '<div style="margin-bottom: 16px;">';
        echo '<label class="form-label"><strong>System Type</strong></label>';
        echo '<select name="type" class="form-select" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 6px; background: var(--color-surface); color: var(--color-text-primary);">';
        echo '<option value="Sega Naomi"'.($row[2] == "Sega Naomi" ? ' selected' : '').'>Sega Naomi</option>';
        echo '<option value="Sega Naomi2"'.($row[2] == "Sega Naomi2" ? ' selected' : '').'>Sega Naomi2</option>';
        echo '<option value="Sega Chihiro"'.($row[2] == "Sega Chihiro" ? ' selected' : '').'>Sega Chihiro</option>';
        echo '<option value="Sega Triforce"'.($row[2] == "Sega Triforce" ? ' selected' : '').'>Sega Triforce</option>';
        echo '</select>';
        echo '</div></div>';
        
        echo '<div class="card-footer dimms-card__footer">';
        echo '<div class="dimms-card__btn-row">';
        echo '<button type="submit" class="btn btn-primary btn-sm">Update</button>';
        echo '<button type="button" class="btn btn-secondary btn-sm" onclick="testDimm(this, \'' . htmlspecialchars($row[1], ENT_QUOTES) . '\')">Test</button>';
        echo '<a href="updatedimms.php?action=delete&linenum='.$i.'&name='.$row[0].'" class="btn btn-secondary btn-sm" onclick="return confirm(\'Are you sure you want to delete this NetDIMM?\')">Delete</a>';
        echo '</div>';
        echo '</div>';
        echo '</form></div>';

    $i++;
}

    echo '</div>'; // Close grid
} // Close else (non-empty CSV)
fclose($f);
?>

<!-- Add NetDIMM Card -->
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h2 class="card-title">Add New NetDIMM</h2>
    </div>
    <form method="post">
        <div class="card-body">
            <?php if($error != '') { ?>
            <div class="alert alert-error" style="margin-bottom: 16px;"><?php echo $error; ?></div>
            <?php } ?>
            
            <div style="margin-bottom: 16px;">
                <label class="form-label"><strong>Name</strong></label>
                <input type="text" name="name" placeholder="Enter Name" class="form-input" value="<?php echo $name; ?>" />
            </div>
            
            <div style="margin-bottom: 16px;">
                <label class="form-label"><strong>IP Address</strong></label>
                <input type="text" name="ipaddress" class="form-input" placeholder="Enter IP Address (e.g., 192.168.1.10)" value="<?php echo $ipaddress; ?>" />
            </div>
            
            <div style="margin-bottom: 16px;">
                <label class="form-label"><strong>System Type</strong></label>
                <select name="type" class="form-select" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 6px; background: var(--color-surface); color: var(--color-text-primary);">
                    <option value="Sega Naomi">Sega Naomi</option>
                    <option value="Sega Naomi2">Sega Naomi2</option>
                    <option value="Sega Chihiro">Sega Chihiro</option>
                    <option value="Sega Triforce">Sega Triforce</option>
                </select>
            </div>
        </div>
        <div class="card-footer dimms-card__footer dimms-card__footer--add">
            <button type="submit" name="submit" class="btn btn-primary">Add NetDIMM</button>
        </div>
    </form>
</div>

</div> <!-- container -->

<script>
function toggleSidebar() {
  const s = document.getElementById('sidebarNav');
  const o = document.getElementById('sidebarOverlay');
  const b = document.getElementById('burgerBtn');
  if (s) s.classList.toggle('open');
  if (o) o.classList.toggle('show');
  if (b) b.classList.toggle('open');
}

function testDimm(btn, ip) {
  const footer = btn.closest('.card-footer') || btn.parentNode;
  let result = footer.querySelector('.dimms-card__test-result');
  if (!result) {
    result = document.createElement('p');
    result.className = 'test-result dimms-card__test-result';
    result.setAttribute('role', 'status');
    footer.appendChild(result);
  }
  btn.disabled = true;
  result.textContent = 'Testing…';
  result.style.color = 'var(--color-text-muted, #888)';

  fetch('pingtest.php?ip=' + encodeURIComponent(ip))
    .then(r => r.json())
    .then(data => {
      result.textContent = data.online ? '✓ ' + data.msg : '✗ ' + data.msg;
      result.style.color = data.online ? 'var(--color-success, #4caf50)' : 'var(--color-error, #f44336)';
    })
    .catch(() => {
      result.textContent = '✗ Request failed';
      result.style.color = 'var(--color-error, #f44336)';
    })
    .finally(() => { btn.disabled = false; });
}
</script>

<script>
if (typeof testDimm === 'undefined') {
function testDimm(btn, ip) {
  let result = btn.nextElementSibling;
  if (!result || !result.classList.contains('test-result')) {
    result = document.createElement('span');
    result.className = 'test-result';
    result.style.cssText = 'margin-left:8px;font-weight:bold;';
    btn.insertAdjacentElement('afterend', result);
  }
  btn.disabled = true;
  result.textContent = 'Testing…';
  fetch('pingtest.php?ip=' + encodeURIComponent(ip))
    .then(r => r.json())
    .then(data => { result.textContent = data.online ? '✓ ' + data.msg : '✗ ' + data.msg; })
    .catch(() => { result.textContent = '✗ Request failed'; })
    .finally(() => { btn.disabled = false; });
}
}
</script>
</body></html>
