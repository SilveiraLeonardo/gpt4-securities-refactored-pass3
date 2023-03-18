
<?php
    include("../common/header.php");
?>

<form action="/api/index.php" method="POST">
    <input type="text" name="file">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stylepath = basename($_POST['file']);

    if (preg_match('/^[a-zA-Z0-9_\-]+$/', $stylepath)) {
        $allowed_files = array('style1.css', 'style2.css', 'style3.css');

        if (in_array($stylepath, $allowed_files)) {
            $file_path = realpath(dirname(__FILE__) . '/' . $stylepath);

            if (strpos($file_path, realpath(dirname(__FILE__))) === 0 && is_file($file_path)) {
                $file_contents = file_get_contents($file_path);
                echo htmlspecialchars(strip_tags($file_contents));
            }
        }
    }
}
?>
