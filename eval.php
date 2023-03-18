
<?php

$maxLength = 100;
$noVariableMessage = 'No variable given';
$allowList = ['variable1', 'variable2', 'variable3']; // Define allowed variables

if (!empty($_GET['variable']) && (strlen($_GET['variable']) <= $maxLength) && ctype_alnum($_GET['variable']) && in_array($_GET['variable'], $allowList)) {
    $variable = filter_var($_GET['variable'], FILTER_SANITIZE_STRING);
    echo htmlentities($variable, ENT_QUOTES, 'UTF-8');
} else {
    echo htmlentities($noVariableMessage, ENT_QUOTES, 'UTF-8');
}
