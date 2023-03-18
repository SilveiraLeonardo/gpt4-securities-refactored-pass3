
<?php include("../common/header.php"); ?>

<?php
hint("something something something placeholder placeholder placeholder");
?>

<form action="/CMD-3/index.php" method="POST">
    Whois: <input type="text" name="domain">
</form>

<pre>
<?php
    $domain = filter_input(INPUT_POST, 'domain', FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
    if ($domain === false) {
        echo "Invalid domain provided.";
        exit;
    }

    // Using the whois library to perform the lookup instead of shell_exec
    require_once 'phpWhois/autoload.php';
    $whois = new Sbaresearch\phpWhois();
    $result = $whois->lookup($domain);

    if ($result['status'] === 'SUCCESS') {
        $output = $result['content'];
    } else {
        $output = $result['status'] . ': ' . $result['message'];
    }

    echo htmlspecialchars($output);
 ?>
</pre>
