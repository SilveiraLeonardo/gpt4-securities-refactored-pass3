
<?php

require_once('../_helpers/strip.php');

$xml = strlen($_GET['xml']) > 0 ? strip_tags($_GET['xml']) : '<root><content>No XML found</content></root>';

$document = new DOMDocument('1.0', 'UTF-8');
$document->loadXML($xml);
$parsedDocument = simplexml_import_dom($document);

echo htmlspecialchars(strip_tags($parsedDocument->content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
