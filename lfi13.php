
<?php
include("../common/header.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file = filter_input(INPUT_POST, 'file', FILTER_SANITIZE_STRING);

    switch ($file) {
        case 'index.php':
            $file_path = 'pages/index.php';
            break;
        case 'about.php':
            $file_path = 'pages/about.php';
            break;
        case 'contact.php':
            $file_path = 'pages/contact.php';
            break;
        default:
            $file_path = 'pages/index.php';
            break;
    }
    include($file_path);
}
?>

<form action="" method="POST">
    <input type="text" name="file">
    <input type="submit" value="Submit">
</form>
