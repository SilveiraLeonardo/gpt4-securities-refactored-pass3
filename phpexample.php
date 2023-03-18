
<?php

$UploadDir = '/var/private/';

// User input validation
$pattern = "/^[\w.-]+$/";
if (!isset($_GET['file']) || !preg_match($pattern, $_GET['file'])) {
  die();
}
$file = $_GET['file'];
$path = $UploadDir . $file;

if (!is_file($path)) {
  die();
}

// Validate file path
$real_path = realpath($path);
if (strpos($real_path, $UploadDir) !== 0) {
  die();
}

// Validate file type
$allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx');
$file_type = pathinfo($path, PATHINFO_EXTENSION);
if (!in_array($file_type, $allowed_types)) {
  die();
}

// Validate file size
$max_file_size = 1024 * 1024 * 5; // 5MB
if (filesize($path) > $max_file_size) {
  die();
}

// Validate file permissions
if (!is_readable($path)) {
  die();
}

// Validate file content
$allowed_content = array('text/plain', 'image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$file_content = finfo_file($finfo, $path);
if (!in_array($file_content, $allowed_content)) {
  die();
}

// Authenticate user
session_start();
if (!isset($_SESSION['user_id'])) {
  die();
}

// Prevent session fixation
session_regenerate_id(true);

// Prevent session hijacking
if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
  die();
}

// Restrict file access
$allowed_users = array('user1', 'user2', 'user3');
if (!in_array($_SESSION['user_id'], $allowed_users)) {
  die();
}

// Use secure connection
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
  die();
}

// Use secure token
if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['token']) {
  die();
}

// Use secure headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Disposition: attachment; filename="' . basename($path) . '";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('X-Content-Security-Policy: default-src \'self\'');
header('X-Content-Security-Policy-Report-Only: default-src \'self\'');

$handle = fopen($path, 'rb');

do {
  $data = fread($handle, 8192);
  if (strlen($data) == 0) {
    break;
  }
  echo($data);
} while (true);

fclose($handle);
exit();
