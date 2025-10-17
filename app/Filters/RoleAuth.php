<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role = $session->get('role');
        $uri = service('uri')->getPath();

        // Allow public pages for guests
        if (!$role) {
            return;
        }

        // Allow all logged-in users to access unified dashboard
        if ($uri === 'dashboard') {
            return;
        }

        // Admin: allow /admin/*
        if ($role === 'admin' && strpos($uri, 'admin') === 0) {
            return;
        }

        // Teacher: allow /teacher/*
        if ($role === 'teacher' && strpos($uri, 'teacher') === 0) {
            return;
        }

        // Student: allow /student/* and /announcements
        if ($role === 'student' && (strpos($uri, 'student') === 0 || $uri === 'announcements')) {
            return;
        }

        // Not permitted
        session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
        return redirect()->to('/announcements');
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}
