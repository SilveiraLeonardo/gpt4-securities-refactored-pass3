
<?php
include("../common/header.php");

// Generate CSRF token
session_start();
if (empty($_SESSION['_token'])) {
    session_regenerate_id(true);
    $_SESSION['_token'] = bin2hex(random_bytes(32));
}
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="file">
    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
    <button type="submit">Submit</button>
</form>

<?php
$allowed_extensions = array('txt', 'html', 'css', 'js');
$allowed_path = '/var/www/html/files/';

if (isset($_POST['file']) && !empty($_POST['file']) && validate_input($_POST['file']) && isset($_POST['_token']) && hash_equals($_SESSION['_token'], $_POST['_token'])) {
    $file_path = realpath_safe($allowed_path . $_POST['file']);

    if ($file_path) {
        $handle = fopen($file_path, 'r');
        if ($handle) {
            echo fread($handle, filesize($file_path));
            fclose($handle);
        }
    } else {
        echo 'Lorem ipsum dolor sit amet consectetur adipisicing elit.' . "\n";
    }
}

function realpath_safe($path) {
    $realpath = realpath($path);
    if (false === $realpath) {
        return false;
    }

    $allowed_path = '/var/www/html/files/';
    if (0 === strpos($realpath, $allowed_path)) {
        return $realpath;
    }

    return false;
}

function validate_input($input) {
    $allowed_extensions = array('txt', 'html', 'css', 'js');
    $file_extension = pathinfo($input, PATHINFO_EXTENSION);

    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $input)) {
        return false;
    }

    if (preg_match('/\.\./', $input) || preg_match('/[\s\t\r\n]/', $input) || preg_match('/[\*\?\|]/', $input) || preg_match('/[\;\&\|\`]/', $input)) {
        return false;
    }

    return true;
}
?>
