
<?php 
include("../common/header.php"); 
session_start();

if(!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="page">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'];?>">
    <button type="submit">Submit</button>
</form>

<?php
$allowed_pages = array('page1.php', 'page2.php', 'page3.php');
if(isset($_POST["page"])) {
    // Validate CSRF Token
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    $inputPage = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_STRING);

    // Use strict validation - only allow alphanumeric characters and dots in filenames.
    if(preg_match('/^[\w-]+\.(php)$/', $inputPage) && in_array($inputPage, $allowed_pages)) {
        include(__DIR__ . '/' . $inputPage);
    } else {
        echo 'Invalid page';
    }
}
?>
