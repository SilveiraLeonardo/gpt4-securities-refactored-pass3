
<?php
    include("../common/header.php");

    $file = filter_input(INPUT_POST, 'file');
    $file_value = $file ? htmlspecialchars($file, ENT_QUOTES, 'UTF-8') : '';
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="file" value="<?php echo $file_value; ?>">
</form>

<?php
    $allowed_files = ['index.php', 'about.php', 'contact.php'];

    if (isset($file) && in_array($file, $allowed_files)) {
        include("pages/" . $file);
    } else {
        include("pages/index.php");
    }
?>
