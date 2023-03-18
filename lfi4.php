
<?php

function sanitize_class_name($input) {
    return preg_match('/^[a-zA-Z0-9_\-]+$/', $input) ? $input : false;
}

?>

<form action="/api/index.php" method="POST">
    <input type="text" name="class" pattern="[a-zA-Z0-9_\-]+" required>
</form>

<?php
$allowed_classes = array('class1', 'class2', 'class3');

if (isset($_POST['class'])) {
    $class = sanitize_class_name($_POST['class']);

    if ($class !== false && in_array($class, $allowed_classes)) {
        $class_file = "includes/class_{$class}.php";
        $real_class_file = realpath($class_file);

        if ($real_class_file !== false && strpos($real_class_file, "includes") === 0 && file_exists($class_file) && is_file($class_file)) {
            include($class_file);
        }
    }
}
?>
