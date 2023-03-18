
<?php include("../common/header.php"); ?>

<?php
hint("something something something placeholder placeholder placeholder");
?>

<form action="/CMD-1/index.php" method="POST">
    <input type="text" name="cmd" pattern="[a-zA-Z0-9_\-\s]+" required>
</form>

<?php
    $allowed_cmds = array('cmd1', 'cmd2', 'cmd3'); // Define allowed commands
    $cmd = htmlspecialchars($_POST["cmd"]);
    if (in_array($cmd, $allowed_cmds)) {
        $output = shell_exec($cmd);
    } else {
        $output = "Invalid command";
    }
    
    $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    echo $output;
?>
