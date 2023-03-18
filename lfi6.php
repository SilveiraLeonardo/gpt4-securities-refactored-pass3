
<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Page</title>
</head>
<body>
    
<form action="/api/index.php" method="POST">
    <input type="hidden" name="csrf-token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="text" name="page">
    <input type="submit" value="Submit">
</form>

<?php
$allowed_pages = array('page1.php', 'page2.php');
$page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_STRING);

if (!empty($page) && in_array($page, $allowed_pages) && hash_equals($_SESSION["csrf_token"], $_POST["csrf-token"])) {
    if (is_file('/pages/' . $page)) {
        include('/pages/' . htmlspecialchars($page));
    }
}
?>

</body>
</html>
