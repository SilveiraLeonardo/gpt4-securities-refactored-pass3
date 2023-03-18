
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/_helpers/strip.php');

$db = new SQLite3('test.db');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    echo 'Usage: ?id=1';
} else {
    try {
        $stmt = $db->prepare('SELECT count(*) FROM secrets WHERE id = ?');
        $stmt->bindValue(1, strip($id), SQLITE3_INTEGER);
        $count = $stmt->execute()->fetchArray()[0];

        if ($count > 0) {
            echo 'Yes!';
        } else {
            echo 'No!';
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        header('Location: error.php');
        exit;
    }
}
