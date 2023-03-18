
<?php
class File {
  public function flag() {
    $this->innocent();
  }
  protected function innocent() {
    echo "Nothing happening here.\n";
  }
}

class GiveFlag extends File {
  private $offset = 23; // Change from public to private.

  protected function innocent() {
    $file = __DIR__ . '/flag.txt';

    // Ensure the file requested is the intended file (flag.txt). Also, ensure proper file permission.
    if (realpath($file) === __DIR__.'/flag.txt' && is_readable($file) && is_file($file) && !is_writable($file)) {
      $stuff = fopen($file, "r");
      fseek($stuff, $this->offset);
      print fread($stuff, 1024); // Limit the size of data read instead of using filesize().
    }
  }
}

class entry {
  public function __construct($data) {
    $data = filter_var($data, FILTER_SANITIZE_STRING);
    if (in_array($data, array('GiveFlag')) && class_exists($data)) {
      $this->awesome = new $data();
    }
  }

  public function __destruct(){
    if (isset($this->awesome)) {
      $this->awesome->flag();
    }
  }
}

$allowed_classes = array('GiveFlag');
$sanitized_input = filter_var($argv[1], FILTER_SANITIZE_STRING);
if (in_array($sanitized_input, $allowed_classes) && class_exists($sanitized_input)) {
  $entry = new entry($sanitized_input);
}
?>
