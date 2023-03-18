
<?php include("../common/header.php"); ?>
<?php hint("something something something about text input fields ..."); ?>

<form action="/CMD-5/index.php" method="POST">
    <input type="text" name="domain">
</form>

<pre>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Apply htmlspecialchars function to user input
    $domain = strtolower(htmlspecialchars($_POST["domain"]));
    // Hardcode the server value
    $server = "whois.publicinterestregistry.net";

    if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $server)) {
        die("Invalid server address");
    }

    // More reliable regex pattern for domain name validation
    if (preg_match('/^[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?(\.[a-z]{2,})+$/', $domain)) {
        $escaped_domain = escapeshellarg($domain);
        $escaped_server = escapeshellarg($server);
        $command = escapeshellcmd("whois -h $escaped_server $escaped_domain");
        $output = shell_exec($command);
        echo htmlspecialchars($output); // htmlspecialchars on output
    } else {
        echo "malformed domain name";
    }
}
?>
</pre>
