<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Anda belum login, silakan login terlebih dahulu!');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Anda bukan admin!');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
