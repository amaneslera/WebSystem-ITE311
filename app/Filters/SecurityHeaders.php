<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SecurityHeaders implements FilterInterface
{
    /**
     * Add security headers to prevent various attacks
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Block path traversal attempts
        $uri = $request->getUri()->getPath();
        $queryString = $request->getUri()->getQuery();
        
        // Check for directory traversal patterns
        $dangerousPatterns = [
            '../',
            '..\\',
            '%2e%2e/',
            '%2e%2e\\',
            '..%2f',
            '..%5c',
            '%00',  // Null byte
            '\\\\',  // Multiple backslashes
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (stripos($uri, $pattern) !== false || stripos($queryString, $pattern) !== false) {
                // Log the attempt
                log_message('warning', 'Path traversal attempt detected: ' . $uri . '?' . $queryString);
                
                // Return 403 Forbidden
                return redirect()->to(base_url())->with('error', 'Invalid request detected.');
            }
        }
        
        // Additional validation: check if URI contains only allowed characters
        // This follows CodeIgniter's permittedURIChars setting
        $allowedPattern = '/^[a-z0-9~%.:_\-\/]+$/i';
        
        if (!preg_match($allowedPattern, urldecode($uri))) {
            log_message('warning', 'Invalid characters in URI: ' . $uri);
            return redirect()->to(base_url())->with('error', 'Invalid request.');
        }
    }

    /**
     * Add security headers to response
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add security headers
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Remove server information
        $response->removeHeader('Server');
        $response->removeHeader('X-Powered-By');
        
        return $response;
    }
}
