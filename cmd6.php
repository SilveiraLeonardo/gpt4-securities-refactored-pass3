
<?php include("../common/header.php"); ?>

<form action="/CMD-6/index.php" method="POST">
    <input type="text" name="domain">
</form>

<pre>
<?php
$domain = filter_input(INPUT_POST, 'domain', FILTER_SANITIZE_STRING);

// Remove the hidden server input and directly assign the server value in the PHP code
$server = 'whois.publicinterestregistry.net';
$valid_servers = ['whois.publicinterestregistry.net', 'whois.internic.net', 'whois.verisign-grs.com'];

if (!in_array($server, $valid_servers)) {
    die('Invalid server');
}

if (preg_match('/^[-a-z0-9]+\.a[cdefgilmnoqrstuwxz]|b[abdefghijmnorstvwyz]|c[acdfghiklmnoruvxyz]|d[ejkmoz]|e[cegrstu]|f[ijkmor]|g[abdefghilmnpqrstuwy]|h[kmnrtu]|i[delmnoqrst]|j[emop]|k[eghimnprwyz]|l[abcikrstuvy]|m[acdeghklmnopqrstuvwxyz]|n[acefgilopruz]|om|p[aefghklmnrstwy]|qa|r[eosuw]|s[abcdeghijklmnortuvyz]|t[cdfghjklmnoprtvwz]|u[agksyz]|v[aceginu]|w[fs]|y[et]|z[amw]|biz|cat|com|edu|gov|int|mil|net|org|pro|tel|aero|arpa|asia|coop|info|jobs|mobi|name|museum|travel|arpa|xn--[a-z0-9]+$/', strtolower($domain)))
{ 
    $output = shell_exec("whois -h " . escapeshellarg($server) . " " . escapeshellarg($domain));
    echo htmlspecialchars(strip_tags($output));
}
else
{
    echo "malformed domain name";
}
?>
</pre>
