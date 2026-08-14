<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        if (!$session->get('sess_id')) {
            return $this->respondUnauthorized();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return;
    }

    private function respondUnauthorized()
    {
        return service('response')
            ->setStatusCode(401)
            ->setJSON([
                'status' => 0,
                'message' => 'Unauthorized. Session not found.'
            ]);
    }
}