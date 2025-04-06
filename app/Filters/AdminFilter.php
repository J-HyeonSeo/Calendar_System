<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\SessionInterface;

class AdminFilter implements FilterInterface
{
    private $session;

    public function __construct() {
        $this->session = \Config\Services::session();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $role = $this->session->get('role_name');

        if ($role !== 'ADMIN') {
            return response()
                ->setStatusCode(302)
                ->setHeader('Location', '/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 아무것도 수행하지 않음.
    }
}