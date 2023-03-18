
<?php

class Login
{
    public $username;
    public $password;
    public $role;
}

function isValidJSON($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

$one = new Login();
$a = json_encode($one);
echo "Example of an object:\n$a\n\n";
echo "FLAG: \n";

// Validate user input
$test = filter_input_array(INPUT_GET, array('data' => FILTER_SANITIZE_STRING));
if (isset($test['data']) && !empty($test['data'])) {
    if (isValidJSON($test['data'])) {
        $data = json_decode($test['data']);
        if (isset($data->username) && isset($data->password) && isset($data->role)) {
            $username = filter_var($data->username, FILTER_SANITIZE_STRING);
            $password = filter_var($data->password, FILTER_SANITIZE_STRING);
            $role = filter_var($data->role, FILTER_SANITIZE_STRING);

            // Load sensitive data from configuration
            $config = parse_ini_file('/path/to/config.ini');
            $correctUsername = $config['username'];
            $correctPassword = $config['password'];
            $correctRole = $config['role'];

            foreach ([$username, $password, $role] as $input) {
                if (!is_string($input) || strlen($input) > 100) {
                    die('Invalid input');
                }
            }

            if (hash_equals($username, $correctUsername) &&
                hash_equals($password, $correctPassword) &&
                hash_equals($role, $correctRole)) {
                // Restrict access to flag file
                if (!defined('FLAG_FILE')) {
                    define('FLAG_FILE', '/path/to/flag.txt');
                }
                if (!is_readable(FLAG_FILE) || !is_file(FLAG_FILE)) {
                    die('Flag is not available!');
                }
                $flag = htmlspecialchars(file_get_contents(FLAG_FILE));
                echo htmlspecialchars(htmlentities($flag));
            } else {
                echo "No flag for you!! Better luck next time!\n";
            }
        } else {
            echo "No flag for you!! Better luck next time!\n";
        }
    } else {
        echo "Invalid Data format! No flag for you!! Better luck next time!\n";
    }
} else {
    echo "No flag for you!! Better luck next time!\n";
}
