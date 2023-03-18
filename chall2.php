
class Example2
{
    function __construct()
    {
        // Ensure any user inputs or other data are validated, sanitized and encrypted if necessary 
        // in context where the object is instantiated
    }

    public static function validateInput($data)
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            if (!is_string($value)) {
                return false;
            }
        }

        return true;
    }

    public static function sanitizeInput($data)
    {
        $sanitized_data = array();
        foreach ($data as $key => $value) {
            $sanitized_data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }
        return $sanitized_data;
    }

    public static function validateAndSanitizeInput($data)
    {
        if (!Example2::validateInput($data)) {
            return false;
        }

        return Example2::sanitizeInput($data);
    }
}

// some PHP code...

// Use server-side sessions instead of cookies for sensitive data
session_start();

// Fetch user data from session
$user_data = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;

// Make sure you validate, sanitize and decrypt (if necessary) sensitive data here
$user_data = Example2::validateAndSanitizeInput($user_data);
if ($user_data) {
    // process data, e.g. database query using prepared statements
} else {
    // Log error details on the server
    error_log('Error: Invalid user data.');

    // Return a generic error message to the end user
    echo 'An error has occurred. Please try again later.';
}

// some PHP code...
