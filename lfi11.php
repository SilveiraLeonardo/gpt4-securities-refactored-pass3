
<?php
session_start();
include("../common/header.php");

// Function to generate and save CSRF token for form
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$stylepath = "";
if (isset($_POST['stylepath']) && isset($_POST['csrf_token'])) {
    // Validate CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $stylepath = filter_input(INPUT_POST, 'stylepath', FILTER_SANITIZE_STRING);
    } else {
        die('Invalid CSRF token');
    }
}

$allowed_paths = array('styles', 'scripts');
$is_allowed_path = false;
$stylepath = basename($stylepath);

foreach ($allowed_paths as $allowed_path) {
    $full_path = realpath("/" . $allowed_path . "/" . $stylepath);
    if (file_exists($full_path)) {
        $is_allowed_path = true;
        $stylepath = $full_path;
        break;
    }
}

if ($is_allowed_path) {
    if (strpos($stylepath, '.php') === false && !is_executable($stylepath) && is_readable($stylepath) && !is_writable($stylepath)) {
        include($stylepath);
    } else {
        die('Invalid file path');
    }
} else {
    die('Invalid file path');
}
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="stylepath" pattern="[a-zA-Z0-9_\-\/]+" required>
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <button type="submit">Submit</button>
</form>
