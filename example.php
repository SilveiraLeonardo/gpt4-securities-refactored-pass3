
<?php
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once('../_helpers/strip.php');

$max_length = 50;
$name_raw = isset($_GET['name']) ? $_GET['name'] : '';
$name = strip_tags($name_raw);
$name = mb_substr($name, 0, $max_length);

$escaped_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
echo 'Hello, ' . $escaped_name;
echo $escaped_name;
