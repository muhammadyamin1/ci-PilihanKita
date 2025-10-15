<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Anda belum login, silakan login terlebih dahulu!');
        }

        // Cek role
        if (!empty($arguments)) {
            if (in_array('admin', $arguments) && session()->get('role') !== 'admin') {
                return redirect()->to('/pemilihan'); // user biasa dialihkan
            }
            if (in_array('user', $arguments) && session()->get('role') !== 'user') {
                return redirect()->to('/dashboard'); // admin dialihkan
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
