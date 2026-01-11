<?php
include 'menu.php';
include 'devicelist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openffb.php">OpenFFB Mapping File Management</a></h1>';
echo '<table class="center" id="options">';
echo '<tr><th>Mapping File</th><th>Actions</th></tr>';

if ($_GET["command"] == 'delete') {
    // SECURITY: Validate file path
    $deletefile = $_GET["filetodelete"] ?? '';
    
    // Validate file path is in expected directory
    $expected_path = '/etc/openffb/games/';
    if (strpos($deletefile, $expected_path) !== 0) {
        header("Location: ffbmapping.php");
        exit;
    }
    
    // Additional validation: only allow alphanumeric, dash, underscore in filename
    $filename = basename($deletefile);
    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $filename)) {
        header("Location: ffbmapping.php");
        exit;
    }
    
    // SECURITY: Use escapeshellarg for parameter
    $command = 'sudo python /sbin/piforce/delete.py ' . escapeshellarg($deletefile);
    shell_exec($command);
    header("Location: ffbmapping.php");
    exit;
}

if(isset($_POST["submit"]))
{
 if(empty($_POST["name"]) || $_POST["name"] == 'New Mapping File')
 {
  $error .= '<label class="text-danger">Filename is required</label>';
 }
 else
 {
  $filename = strtolower($_POST["name"]);
  $result = str_replace(" ", "-", $filename);
 }

 if($error == '')
 {
  $newfile = fopen('/etc/openffb/games/'.$result, "w");
  fclose($newfile);
  echo "<meta http-equiv='refresh' content='1'>";
  $error = '<label class="text-success">Entry Added Successfully</label>';
  $name = '';
 }
}


// SECURITY: Static command with no user input
$command = 'sudo python /sbin/piforce/mappingfiles.py';
shell_exec($command);

$mappingfiles = scandir('/etc/openffb/games');

for ($i = 2; $i < count($mappingfiles); $i++) {

$mappingfilename = $mappingfiles[$i];
$mappingfilepath = '/etc/openffb/games/'.$mappingfilename;

echo '<tr>';
// SECURITY: HTML escape and URL encode output
echo '<td>' . htmlspecialchars($mappingfilename, ENT_QUOTES, 'UTF-8') . '</td>';
echo '<td><a href="editor.php?mode=ffb&mappingfile=' . urlencode($mappingfilepath) . '">edit</a> / <a href="ffbmapping.php?command=delete&filetodelete=' . urlencode($mappingfilepath) . '">delete</a></td>';
echo '</tr>';
}

echo '<tr><form method="post">';
echo '<td><input type="text" name="name" onfocus="this.value=\'\'" placeholder="Enter File Name" class="form-control" value="" /></td>';
echo '<td><input type="submit" name="submit" class="btn btn-info" value="Add Entry" /></td></form></tr></table>';

echo '</p><center></body></html>';
?>