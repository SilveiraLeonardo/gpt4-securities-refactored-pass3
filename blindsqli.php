
<?php

require_once('../_helpers/strip.php');

// this database contains a table with 2 rows
// This is my first secret (ID = 1)
// This is my second secret (ID = 2)
$db = new SQLite3('test.db');

// Improved data validation: verify that the 'id' is set and it's a positive integer.
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
  echo 'Usage: ?id=1';
} else {

  // Access control example: check if user is logged in and authorized.
  session_start();
  if (isset($_SESSION['user_id']) && isset($_SESSION['is_authorized']) && $_SESSION['is_authorized'] === true) {
    $stmt = $db->prepare('select count(*) from secrets where id = ?');
    $stmt->bindValue(1, strip($_GET['id']), SQLITE3_INTEGER);
    $count = $stmt->execute()->fetchArray()[0];

    if ($count > 0) {
      // The access control check has passed, and the user has permission to view the secret.
      echo htmlspecialchars('Yes!', ENT_QUOTES, 'UTF-8');
    } else {
      // The access control check has failed, or the secret doesn't exist.
      echo htmlspecialchars('No!', ENT_QUOTES, 'UTF-8');
    }
  } else {
    echo "Unauthorized access. Please log in.";
  }
}
