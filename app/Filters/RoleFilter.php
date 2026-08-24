<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Usage in Routes.php: 'filter' => 'role:admin,hr'
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session      = session();
        $allowedRoles = $arguments ?? [];
        $userRole     = $session->get('role');

        if (! in_array($userRole, $allowedRoles, true)) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here.
    }
}
