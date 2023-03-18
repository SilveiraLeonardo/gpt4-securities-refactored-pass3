
<?php
// CSRF token check
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo '<pre>Invalid CSRF token.</pre>';
    return;
}

// Validate the file type, size, and actual content
$allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);
list($width, $height, $type, $attributes) = getimagesize($_FILES['uploaded']['tmp_name']);

$uploaded_size = $_FILES['uploaded']['size'];
if (in_array($type, $allowed_types) && $uploaded_size < 100000) {

    // Validate the file name
    $allowed_extensions = array('jpg', 'jpeg', 'png');
    $file_extension = pathinfo($_FILES['uploaded']['name'], PATHINFO_EXTENSION);

    if (!in_array($file_extension, $allowed_extensions)) {
        $html .= '<pre>Your image was not uploaded. We can only accept JPEG or PNG images.</pre>';
        return;
    }

    // Validate the user input
    if (!preg_match('/^[A-Za-z0-9_\-\.]+$/', $_FILES['uploaded']['name'])) {
        $html .= '<pre>Your image was not uploaded. The file name contains invalid characters: ' . htmlspecialchars($_FILES['uploaded']['name']) . '</pre>';
        return;
    }

    // Generate a random unique filename
    $filename = uniqid() . '.' . $file_extension;
    $target_path = 'uploads/' . $filename;
    $target_path = realpath($target_path);

    // Ensure $target_path is properly sanitized and validated
    if (strpos($target_path, realpath('uploads/')) !== 0) {
        $html .= '<pre>Your image was not uploaded. Invalid file path.</pre>';
        return;
    }

    // Can we move the file to the upload folder?
    if (!move_uploaded_file($_FILES['uploaded']['tmp_name'], $target_path)) {
        // No
        $html .= '<pre>Your image was not uploaded.</pre>';
    } else {
        // Yes!
        chmod($target_path, 0600); // Set secure file permissions
        $html .= "<pre>" . htmlspecialchars($target_path) . " successfully uploaded!</pre>";
    }
} else {
    // Invalid file
    $html .= '<pre>Your image was not uploaded. We can only accept JPEG or PNG images with a maximum file size of 100KB.</pre>';
}
?>
