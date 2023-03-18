
class Example1
{
    private $cache_file;
    private $file_path;

    function __construct($cache_file)
    {
        $this->cache_file = $cache_file;
        $this->file_path = "/var/www/cache/tmp/{$this->cache_file}";
    }

    public function getFilePath()
    {
        return $this->file_path;
    }

    function __destruct()
    {
        $file = $this->file_path;
        
        if (file_exists($file) && is_file($file) && is_writable($file)) {
            $allowed_file_types = array('jpg', 'jpeg', 'png', 'gif');
            $allowed_file_size = 1024 * 1024 * 5; // 5MB
            
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_file_types) && filesize($file) <= $allowed_file_size) {
                unlink($file);
            }
        }
    }
}

// Sanitize user_data
$user_data = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

// Validate user input
if (!preg_match('/^[a-zA-Z0-9]+$/', $user_data)) {
   throw new Exception('Invalid user input');
}

$cache_file = hash('sha256', $user_data);
$example1 = new Example1($cache_file);
$file = $example1->getFilePath();

// Insert logic for interacting with existing files or writing new files to the cache
