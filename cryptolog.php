
<?php
include("config.php");
require_once("kontrol.php");

$opt = isset($_POST['opt']) ? strtolower(trim($_POST['opt'])) : '';

// Check if $opt is one of the allowed options: 'del', 'add', 'check', 'mount'
if (!in_array($opt, ['del', 'add', 'check', 'mount'])) {
    die("Invalid operation");
}

$lsid = isset($_POST['lsid']) ? intval($_POST['lsid']) : 0;
$sharetype = isset($_POST['lssharetype']) ? filter_var($_POST['lssharetype'], FILTER_SANITIZE_STRING) : '';
$remoteaddress = isset($_POST['lsremoteaddress']) ? filter_var($_POST['lsremoteaddress'], FILTER_VALIDATE_IP) : '';
$sharefolder = isset($_POST['lssharefolder']) ? filter_var($_POST['lssharefolder'], FILTER_SANITIZE_STRING) : '';
$user = isset($_POST['lsuser']) ? filter_var($_POST['lsuser'], FILTER_SANITIZE_STRING) : '';
$pass = isset($_POST['lspass']) ? filter_var($_POST['lspass'], FILTER_SANITIZE_STRING) : '';
$domain = isset($_POST['lsdomain']) ? filter_var($_POST['lsdomain'], FILTER_SANITIZE_STRING) : '';

$dbConn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_DATABASE, DB_USER, DB_PASS);
$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

include("classes/logshares_class.php");

if ($opt == 'del') {
    $stmt = $dbConn->prepare("DELETE FROM logshares WHERE id=?");
    $stmt->execute([$lsid]);
} else if ($opt == 'add') {
    // Note: Now using bcrypt hashing algorithm for the password
    $hashedPass = password_hash($pass, PASSWORD_BCRYPT);
    $stmt = $dbConn->prepare("INSERT INTO logshares (sharetype, remoteaddress, sharefolder, user, pass, domain) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$sharetype, $remoteaddress, $sharefolder, $user, $hashedPass, $domain]);
} else if ($opt == 'check') {
    echo htmlspecialchars(cLogshares::fTestFileshare("/mnt/logsource_" . $lsid . "_" . $sharetype));
} else if ($opt == 'mount') {
    cLogshares::fMountFileshareOnly($dbConn, $lsid, $sharetype);
    echo htmlspecialchars(cLogshares::fTestFileshare("/mnt/logsource_" . $lsid . "_" . $sharetype));
    cLogshares::fCheckFilePermissions("/mnt/logsource_" . $lsid . "_" . $sharetype);
}

function fTestFileshare($sharefolder)
{
    $output = shell_exec('sudo /opt/cryptolog/scripts/testmountpoint.sh ' . escapeshellarg($sharefolder) . ' 2>&1');
    return htmlspecialchars(trim($output));
}

function fCheckFilePermissions($sharefolder)
{
    $output = shell_exec('sudo /opt/cryptolog/scripts/checkfilepermissions.sh ' . escapeshellarg($sharefolder) . ' 2>&1');
    return htmlspecialchars(trim($output));
}
?>
