
// Disable external entity loading
libxml_disable_entity_loader(true);

$xmlfile = file_get_contents('php://input');
$dom = new DOMDocument();

// Remove DTD loading and only disable external entities
$dom->loadXML($xmlfile, LIBXML_NOENT);

$info = simplexml_import_dom($dom);

// Input validation
$name = filter_var($info->name, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
if (empty($name) || strlen($name) > 100) {
    echo "Please provide a valid name.";
    exit;
}

$tel = filter_var($info->tel, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
if (empty($tel) || strlen($tel) > 20) { // Add any necessary validation checks
    echo "Please provide a valid phone number.";
    exit;
}

$email = filter_var($info->email, FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    echo "Please provide a valid email address.";
    exit;
}

$rawPassword = filter_var($info->password, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
if (empty($rawPassword) || strlen($rawPassword) < 8) {
    echo "Please provide a password with a minimum length of 8 characters.";
    exit;
}

$password = password_hash($rawPassword, PASSWORD_ARGON2ID, [
    'memory_cost' => 1<<17, // 128MB
    'time_cost'   => 4,
    'threads'     => 2,
]);

// Store user data and check if the email is already registered
// ...

// Replace the email-specific error message with a generic one
echo htmlspecialchars(
    htmlentities("Sorry, an error occurred. Please try again.", ENT_QUOTES | ENT_HTML5, 'UTF-8', true),
    ENT_QUOTES | ENT_HTML5, 'UTF-8', true
);
