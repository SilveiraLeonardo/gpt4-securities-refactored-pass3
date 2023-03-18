
session_start();
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;
?>

<?php include("../common/header.php"); ?>

<form action="/api/index.php" method="POST">
    <input type="text" name="file">
    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
</form>

<?php
   $file = filter_input(INPUT_POST, 'file', FILTER_VALIDATE_URL);
   $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_STRING);

   if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
       exit("CSRF token mismatch.");
   }

   switch ($file) {
       case 'index.php':
       case 'about.php':
       case 'contact.php':
           $file_path = realpath("pages/$file");
           if (strpos($file_path, realpath("pages/")) === 0 && is_file($file_path) && is_readable($file_path)) {
               include($file_path);
           } else {
               include("index.php");
           }
           break;
       default:
           include("index.php");
           break;
   }
?>
