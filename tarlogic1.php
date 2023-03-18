
<?php
class warm {
    private $dir = ".";
    
    // Getter method to access private property $dir
    public function getDir() {
        return $this->dir;
    }
    
    // Set the dir property using a secure method
    public function setDir($dir) {
        $allowedDirs = array('/var/www/html/', '/var/www/uploads/');
        
        if (in_array($dir, $allowedDirs)) {
            $this->dir = $dir;
        } else {
            die('Unauthorized access!');
        }
    }

    public function displayFiles() {
        echo "This folder contains:\n";
        $files = scandir($this->dir);
        
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..')) && in_array($file, array('index.php', 'style.css', 'script.js'))) {
                echo $file . "\n";
            }
        }
    }
}

$test = new warm();
$a = json_encode(array('dir' => $test->getDir()));
echo "Example of an object:\n$a\n\n";

$input = isset($argv[1]) ? $argv[1] : die("Input required");

// Sanitize and validate user input to prevent code injection
$input = filter_var(trim($input), FILTER_SANITIZE_STRING);
if (empty($input)) {
    die('Invalid input format!');
}

$json = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Invalid input format!');
}

$data = new warm();
$data->setDir($json['dir']);

$data->displayFiles();
?>
