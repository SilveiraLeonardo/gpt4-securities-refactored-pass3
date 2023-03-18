
<?php
include("../common/header.php");

$allowed_libraries = array('library1', 'library2', 'library3');
$library_name = $_POST['library'];

if (in_array($library_name, $allowed_libraries, true)) {
    $file_name = basename($library_name);
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

    if ($file_extension == 'php') {
        $file_path = realpath("includes/" . $library_name . ".php");

        if (strpos($file_path, realpath("includes/")) === 0 && is_file($file_path) && preg_match('/^[a-zA-Z0-9_\-]+\.php$/', $library_name) === 1) {
            include($file_path);
        }
    }
}
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <input type="text" name="library" pattern="[a-zA-Z0-9_\-]+\.php$" value="<?php echo htmlspecialchars(strip_tags($library_name)); ?>" required>
</form>
