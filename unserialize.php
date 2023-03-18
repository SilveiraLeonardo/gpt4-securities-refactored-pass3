
class ViewFile {
    public $filename = '';

    public function __toString() {
        if (file_exists($this->filename)) {
            return file_get_contents($this->filename);
        }
        return "";
    }
}

// Implement authentication and access control here

if (isset($_GET['page'])) {
    $data = base64_decode($_GET['page'], true);

    // Ensure that the input contains only a valid JSON string
    if ($data !== false) {
        $data = json_decode($data, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            if (isset($data['filename'])) {
                // Validate the filename
                $filename = realpath($data['filename']);
                $baseFilename = basename($filename);
                $allowedPath = realpath('/path/to/files/');
                if (strpos($filename, $allowedPath . DIRECTORY_SEPARATOR . $baseFilename) === 0 && !preg_match('/\.\./', $filename) && preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
                    // Authentication and access control succeeded

                    $pdfobject = new ViewFile();
                    $pdfobject->filename = $filename;
                } else {
                    // Error handling, e.g., show a 404 page if the file is not allowed
                }
            }
        }
    }
} else {
    $pdfobject = new File(); // Make sure the 'File' class is properly defined and implemented
}
