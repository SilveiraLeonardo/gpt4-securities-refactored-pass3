
<?php

// Start a user session to implement CSRF protection
session_start();

// Generate a CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include the secure header.php
include("../common/header.php");

// List of allowed commands
$allowed_commands = ['command1', 'command2', 'command3'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the CSRF token matches
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $cmd = strip_tags($_POST["cmd"]);
        $cmd = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $cmd); // Remove any special characters to prevent command injection

        // Check if the command is in the list of allowed commands
        if (in_array($cmd, $allowed_commands)) {
            // Securely execute the command using `exec` instead of `shell_exec`
            $output = [];
            exec($cmd, $output);

            // Sanitize output from the command
            $sanitized_output = [];
            foreach ($output as $line) {
                $sanitized_output[] = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
            }
        } else {
            echo "<p>Error: Command not allowed.</p>";
        }
    } else {
        echo "<p>Error: Invalid CSRF token.</p>";
    }
}

?>

<!-- Add the CSRF token as a hidden input field in the form -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    <input type="text" name="cmd" pattern="[a-zA-Z0-9_\-\s]+" required>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="submit" value="Submit">
</form>

<?php
// Print the sanitized output
if (isset($sanitized_output) && !empty($sanitized_output)) {
    echo "<pre>";
    foreach ($sanitized_output as $line) {
        echo $line . "\n";
    }
    echo "</pre>";
}

?>
