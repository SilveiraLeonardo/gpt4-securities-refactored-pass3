
<?php include("../common/header.php"); ?>

<!-- Use POST method instead of GET to prevent sensitive information from being visible in the URL -->
<form action="/api/index.php" method="POST">
    <input type="text" name="file">
</form>

<?php
// Use $_POST instead of $_GET
if (isset($_POST['file'])) {
    $file = $_POST['file'];
    $allowed_extensions = array('txt', 'html', 'css', 'js');

    // Get and validate the file extension (lowercase)
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // Sanitize user input by removing null bytes
    $file = str_replace("\0", '', $file);

    // Check for path traversal (Updated to make it more robust)
    if (preg_match('/(?:\.|^)\.(?:\.|^)/', $file) || strpos($file, '~') !== false) {
        echo 'Lorem ipsum dolor sit amet consectetur adipisicing elit.' . "\n";
        exit;
    }

    if (in_array($extension, $allowed_extensions) && substr($file, 0, 1) != '/') {
        $file_path = realpath(__DIR__ . '/' . $file);
        // Ensure file is within intended directory
        if (strpos($file_path, __DIR__) === 0 && is_file($file_path)) {
            // Use readfile() instead of file_get_contents() and htmlspecialchars()
            // readfile() is more memory-efficient and prevents code execution
            readfile($file_path);
        } else {
            echo 'Lorem ipsum dolor sit amet consectetur adipisicing elit.' . "\n";
        }
    } else {
        echo 'Lorem ipsum dolor sit amet consectetur adipisicing elit.' . "\n";
    }
}
?>
