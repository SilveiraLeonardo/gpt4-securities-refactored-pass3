
<?php
// Set session cookie flags
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Use this only if your application supports HTTPS (otherwise, remove it)
ini_set('session.cookie_samesite', 'Strict');

// Start the session
session_start();

// Implement session timeout by checking for user inactivity (1800 seconds = 30 minutes)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update the last activity time
?>
