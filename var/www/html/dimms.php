<?php

// Load UI mode helper
include_once 'ui_mode.php';
$ui_mode = get_ui_mode();

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="NetDIMM Management">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';

// Load CSS based on UI mode
load_ui_styles();

// Only include old menu for classic UI
if ($ui_mode === 'classic') {
    include 'menu_include.php';
}

function pinger($address){
        $command = "fping -c1 -t500 $address";
        exec($command, $output, $status);
        if($status === 0){
            return true;
        }else{
            return false;
        }
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

// Modern UI wrapper
if ($ui_mode === 'modern') {
    echo '<button class="burger-menu" id="burgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu"><span></span><span></span><span></span></button>';
    echo '<div class="sidebar-nav" id="sidebarNav">';
    echo '<nav>';
    echo '<a href="menu.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">📊</span><span class="sidebar-nav-label">Dashboard</span></a>';
    echo '<a href="gamelist.php?display=all" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎮</span><span class="sidebar-nav-label">Games</span></a>';
    echo '<a href="dimms.php" class="sidebar-nav-item active">';
    echo '<span class="sidebar-nav-icon">💾</span><span class="sidebar-nav-label">NetDIMMs</span></a>';
    echo '<a href="setup.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">⚙️</span><span class="sidebar-nav-label">Setup</span></a>';
    echo '<a href="ui-mode-switcher.php" class="sidebar-nav-item">';
    echo '<span class="sidebar-nav-icon">🎨</span><span class="sidebar-nav-label">UI Mode</span></a>';
    echo '</nav></div>';
    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>';
    echo '<div class="main-content">';
    echo '<div class="container" style="padding: 20px;">';
    echo '<div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 24px;">';
    echo '<h1 class="text-3xl" style="margin: 0;">💾 NetDIMM Management</h1>';
    echo '<a href="dimmscanner.php" class="btn btn-primary">🔍 Scan Network</a>';
    echo '</div>';
} else {
    echo '<section><center>';
    echo '<h1><a href="setup.php">Manage Netdimms</a></h1><br>';
}
$f = fopen("csv/dimms.csv", "r");
$headers = ($row = fgetcsv($f));
$i = 1;
$row = fgetcsv($f);

if ($i == 1 and $row[1] == null){
    if ($ui_mode === 'modern') {
        echo '<div class="empty-state">';
        echo '<div class="empty-state-icon">💾</div>';
        echo '<h2>No NetDIMMs Configured</h2>';
        echo '<p>Add NetDIMMs manually below or scan your network to find them automatically.</p>';
        echo '<a href="dimmscanner.php" class="btn btn-primary">🔍 Scan Network</a>';
        echo '</div>';
    } else {
        echo '<b><div class="offline">No Netdimms Configured</div></b><br>';
        echo 'You can add dimms manually here or scan for netdimms using the <a href="dimmscanner.php">Netdimm Scanner</a><br><br>';
    }
} else {
rewind($f);
$headers = ($row = fgetcsv($f));

if ($ui_mode === 'modern') {
    echo '<div class="grid grid-cols-3" style="margin-bottom: 32px;">';
}

while (($row = fgetcsv($f)) !== false) {
    $isOnline = pinger($row[1]);
    
    if ($ui_mode === 'modern') {
        // Modern UI: Card layout
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
        
        echo '<div class="card-footer">';
        echo '<button type="submit" class="btn btn-primary btn-sm">Update</button>';
        echo '<a href="updatedimms.php?action=delete&linenum='.$i.'&name='.$row[0].'" class="btn btn-secondary btn-sm" onclick="return confirm(\'Are you sure you want to delete this NetDIMM?\')">Delete</a>';
        echo '</div>';
        echo '</form></div>';
    } else {
        // Classic UI: Table layout
        echo '<div class="box1">';
        echo '<html><body><table class="center" id="dimms">';
        echo '<form action="updatedimms.php" method="get">';
        echo "<tr>";
        foreach ($row as $cell) {
            echo '<td><b>Name</b></td>';
            echo '<td><input type="text" name="name" placeholder="'.$row[0].'" class="form-control" size="12" value="'.$row[0].'" /></td>';
            echo '<tr><td><b>IP Address</b></td>';
            echo '<input type="hidden" id="ip" name="ip" value="'.$row[1].'">';
            echo '<input type="hidden" id="action" name="action" value="update">';
            echo '<input type="hidden" id="linenum" name="linenum" value="'.$i.'">';
            if ($isOnline){
                echo '<td><b><span class="online">'.$row[1].' (ONLINE)</span></b></td>';}
            else {
                echo '<td><b><span class="offline">'.$row[1].' (OFFLINE)</span></b></td>';}
            echo '<tr><td><b>Type<b></td>';
            echo '<td><select name="type"><option value="Sega Naomi"';
            if ($row[2] == "Sega Naomi"){echo ' selected="selected"';}
            echo '>Sega Naomi</option><option value="Sega Naomi2"';
            if ($row[2] == "Sega Naomi2"){echo ' selected="selected"';}
            echo '>Sega Naomi2</option><option value="Sega Chihiro"';
            if ($row[2] == "Sega Chihiro"){echo ' selected="selected"';}
            echo '>Sega Chihiro</option><option value="Sega Triforce"';
            if ($row[2] == "Sega Triforce"){echo ' selected="selected"';}
            echo '>Sega Triforce</option></select></td></tr>';
            echo '</table><br>';
            echo '<input type="submit" class="dropbtn" value="Update"></form>';
            echo ' <a href="updatedimms.php?action=delete&linenum='.$i.'&name='.$row[0].'" style="font-weight:normal" class="dropbtn">Delete</a></span>';
            $i++;
            break;
        }
        echo "</tr></table></div><br>";
    }
    $i++;
}

if ($ui_mode === 'modern') {
    echo '</div>'; // Close grid
}
} // Close else block from line 130
fclose($f);
?>

<?php if ($ui_mode === 'modern') { ?>
<!-- Modern UI: Add NetDIMM Card -->
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
        <div class="card-footer">
            <button type="submit" name="submit" class="btn btn-primary">Add NetDIMM</button>
        </div>
    </form>
</div>

</div></div> <!-- Close main-content and container -->

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebarNav');
  const overlay = document.getElementById('sidebarOverlay');
  const burger = document.getElementById('burgerBtn');
  sidebar.classList.toggle('open');
  overlay.classList.toggle('show');
  burger.classList.toggle('open');
}
</script>

<?php } else { ?>
<!-- Classic UI: Add NetDIMM Form -->
<div class="box1">
<html><body><table class="center" id="dimms">
<tr>
    <form method="post">
      <tr><td><b>Name</b></td><td><input type="text" name="name" placeholder="Enter Name" class="form-control" size="12" value="<?php echo $name; ?>" /></td></tr>
      <tr><td><b>IP Address</b></td><td><input type="text" name="ipaddress" class="form-control" placeholder="Enter IP Address" size="14" value="<?php echo $ipaddress; ?>" /></td></tr>
      <tr><td><b>Type</b></td><td><select name="type"><option value="Sega Naomi">Sega Naomi</option><option value="Sega Naomi2">Sega Naomi2</option><option value="Sega Chihiro">Sega Chihiro</option><option value="Sega Triforce">Sega Triforce</option></select></td></tr></table>
      <br><input type="submit" name="submit" class="dropbtn" value="Add Entry" />
    </form>
</tr>
</div><br>

<b><?php echo $error; ?></b><br>
</center>
<?php } ?>

</body></html>
