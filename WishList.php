
<?php
class Challenge {
    const UPLOAD_DIRECTORY = './solutions/';
    private $file;
    private $whitelist;
    private $allowed_extensions;
    private $max_filesize;
    private $valid_mimetypes = array('text/plain');

    public function __construct($file) {
        $this->file = $file;
        $this->whitelist = array_map(function($num) { return strval($num) . '.txt'; }, range(1, 24));
        $this->allowed_extensions = array('txt');
        $this->max_filesize = 1024 * 1024; // 1MB
    }

    public function upload() {
        if ($this->validateInput()) {
            $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
            if (in_array($extension, $this->allowed_extensions) &&
                $this->file['size'] <= $this->max_filesize &&
                in_array(mime_content_type($this->file['tmp_name']), $this->valid_mimetypes)) {
                $filename = basename($this->file['name']);
                $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);
                $filename = uniqid('', true) . '.txt';
                $filepath = self::UPLOAD_DIRECTORY . $filename;
                if (is_uploaded_file($this->file['tmp_name']) &&
                    move_uploaded_file($this->file['tmp_name'], $filepath)) {
                    chmod($filepath, 0440);
                    chown($filepath, 'www-data');
                    chgrp($filepath, 'www-data');
                }
            }
        }
    }

    public function validateInput() {
        if (!in_array($this->file['name'], $this->whitelist)) {
            return false;
        }
        $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        if (!in_array($extension, $this->allowed_extensions)) {
            return false;
        }
        $filename = basename($this->file['name']);
        if (preg_match('/[^a-zA-Z0-9_\-\.]/', $filename)) {
            return false;
        }
        if ($this->file['size'] > $this->max_filesize) {
            return false;
        }
        if (!in_array(mime_content_type($this->file['tmp_name']), $this->valid_mimetypes)) {
            return false;
        }
        return true;
    }
}

$challenge = new Challenge($_FILES['solution']);
if ($challenge->validateInput()) {
    $challenge->upload();
}
?>
