
<?php
// Securely sign header.php, then store the signature in header.sig and public key in public.pem
$filename = "../common/header.php";
$signature_file = "../common/header.sig";
$public_key_file = "../common/public.pem";

// Read the contents
$data = file_get_contents($filename);
$signature = file_get_contents($signature_file);
$public_key = openssl_pkey_get_public(file_get_contents($public_key_file));

// Verify the signature
if (openssl_verify($data, $signature, $public_key) === 1) {
    include($filename);
} else {
    die("Error: Include file failed signature check.");
}
?>

<?php
require_once 'vendor/autoload.php';
use phpWhois\Whois;

$whois = new Whois();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain = filter_input(INPUT_POST, 'domain', FILTER_SANITIZE_STRING);

    $result = $whois->lookup($domain);

    // Ensure that the result is properly encoded
    $output = htmlspecialchars(print_r($result, true), ENT_QUOTES | ENT_HTML5);
}
?>

<form action="/CMD-4/index.php" method="POST">
    <input type="text" name="domain">
</form>

<pre><?php echo $output ?? ''; ?></pre>
