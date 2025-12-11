<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimiter implements FilterInterface
{
    private $maxAttempts = 10;
    private $decayMinutes = 1;
    
    public function before(RequestInterface $request, $arguments = null)
    {
        $identifier = $this->getIdentifier($request);
        $cacheKey = 'rate_limit_' . $identifier . '_' . md5($request->getUri()->getPath());
        
        $cache = \Config\Services::cache();
        $attempts = $cache->get($cacheKey) ?? 0;
        
        if ($attempts >= $this->maxAttempts) {
            return \Config\Services::response()
                ->setStatusCode(429)
                ->setJSON([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                    'retry_after' => $this->decayMinutes * 60
                ]);
        }
        
        $cache->save($cacheKey, $attempts + 1, $this->decayMinutes * 60);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
    
    private function getIdentifier(RequestInterface $request): string
    {
        $userId = session()->get('user_id');
        if ($userId) {
            return 'user_' . $userId;
        }
        
        return 'ip_' . $request->getIPAddress();
    }
}
