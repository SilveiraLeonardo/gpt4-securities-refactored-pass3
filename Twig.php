
// composer require "twig/twig"
require 'vendor/autoload.php';

class Template {
    private $twig;

    public function __construct() {
        $indexTemplate = '<img ' .
            'src="https://loremflickr.com/320/240">' .
            '<a href="{{link}}">Next slide »</a>';

        // Default twig setup, simulate loading
        // index.html file from disk
        $loader = new Twig\Loader\ArrayLoader([
            'index.html' => $indexTemplate
        ]);
        
        $this->twig = new Twig\Environment($loader, [
            'autoescape' => 'html', // Enable autoescape to prevent XSS
        ]);
    }

    public function getNextSlideUrl() {
        $nextSlide = filter_var($_GET['nextSlide'], FILTER_VALIDATE_URL);
        if ($nextSlide) {
            // Check for valid URL schemes (http or https)
            if (parse_url($nextSlide, PHP_URL_SCHEME) === 'http' || parse_url($nextSlide, PHP_URL_SCHEME) === 'https') {
                $domain = parse_url($nextSlide, PHP_URL_HOST);
                $allowed_domains = ["example1.com", "example2.com"];
                if (in_array($domain, $allowed_domains)) {
                    return htmlspecialchars($nextSlide, ENT_QUOTES, 'UTF-8');
                }
            }
        }
        return "";
    }

    public function render() {
        $nextSlide = $this->getNextSlideUrl();
        if ($nextSlide) {
            echo $this->twig->render('index.html', [
                'link' => $nextSlide
            ]);
        } else {
            // Show an error message or render a different template with a default URL
            echo "Error: Invalid URL. Please provide a valid URL from the allowed domains.";
        }
    }
}

$template = new Template();
$template->render();
