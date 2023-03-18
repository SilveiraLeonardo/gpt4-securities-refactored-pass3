
<?php
include("../common/header.php");

$file = filter_input(INPUT_POST, 'file', FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[\w-]+\.php$/")));
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="file" value="<?= htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>">
</form>

<?php

function is_valid_file($file, $allowed_files) {
    $file_path = realpath("pages/$file");
    
    $is_valid_path = $file_path && strpos($file_path, realpath('pages')) === 0;
    $is_valid_file = is_file($file_path) && is_readable($file_path);

    return in_array($file, $allowed_files) && $is_valid_path && $is_valid_file;
}

$allowed_files = array('index.php', 'about.php', 'contact.php');

if (isset($file) && is_valid_file($file, $allowed_files)) {
    include(basename(realpath("pages/" . $file)));
} else {
    include("index.php");
}
?>
