
<?php
session_start();
include("../common/header.php");

// Generate a CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$allowed_libraries = array('library1', 'library2', 'library3');
$library = NULL;

// Verify the CSRF token and check if the library is in the allowed list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']) && in_array($_POST['library'], $allowed_libraries)) {
    $library = basename($_POST['library']);
}
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="library">
    <!-- Include the CSRF token in the form -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="submit" value="Submit">
</form>

<?php
if ($library) {
    $library_path = "includes/" . $library . ".php";
    if (file_exists($library_path) && strncmp($library_path, "includes/", 9) === 0 && preg_match('/^[a-zA-Z0-9_\-\.]+$/', $library) && !preg_match('/[\/\\\\]/', $library)) {
        $library_path = escapeshellcmd($library_path);
        include(htmlspecialchars($library_path, ENT_QUOTES, 'UTF-8'));
    }
}
?>
