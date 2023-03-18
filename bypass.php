
<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $fileContent['file'] = false;
    header('Content-Type: application/json; charset=utf-8');
    if (isset($_POST['file'])) {
        $inputFileName = filter_input(INPUT_POST, 'file', FILTER_SANITIZE_STRING);
        
        // Use a regular expression to check for allowed characters and file extension.
        $allowedCharacters = "/^[a-z0-9_]+\.txt$/i";
        $validFilename = preg_match($allowedCharacters, $inputFileName);
        
        if ($validFilename) {
            // Prevent file execution by moving the uploaded files to a non-webroot directory.
            $basePath = "/var/private_files/";
            $filePath = $basePath . basename($inputFileName);
            $realPath = realpath($filePath);
            
            if ($realPath && file_exists($realPath) && is_file($realPath) && pathinfo($realPath, PATHINFO_EXTENSION) == 'txt') {
                $file = fopen($realPath, "r");
                $maxFileSize = 1024; // Define a maximum file size limit, for example, 1024 bytes
                $fileContent['file'] = fread($file, min(filesize($realPath), $maxFileSize));
                fclose($file);
            }
        }
        echo json_encode($fileContent);
    }
}
