
<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YourController extends AbstractController
{
    /**
     * @Route("/your_route", name="your_route_name")
     */
    public function new_http_param(Request $request)
    {
        // Consider implementing rate limiting to prevent abuse and attacks like brute force.
        
        $code = $request->request->get("code");
        $code = $this->sanitizeInput($code);

        // If you don't need to hash the code, you can remove this line.
        $code = password_hash($code, PASSWORD_BCRYPT);

        $response = new Response();
        $response->setContent($code);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Length', strlen($code));

        return $response;
    }
    
    private function sanitizeInput($input)
    {
        $input = filter_var($input, FILTER_SANITIZE_STRING);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $input = strip_tags($input);
        $input = trim($input);

        return $input;
    }
}
