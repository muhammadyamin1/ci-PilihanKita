<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function process()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Contoh sederhana: admin / user
        if ($username === 'admin' && $password === 'admin') {
            return redirect()->to('/dashboard');
        } elseif ($username === 'user' && $password === 'user') {
            return redirect()->to('/pemilihan');
        } else {
            return redirect()->back()->with('error', 'Username atau password salah!');
        }
    }

    public function dashboard()
    {
        return view('auth/dashboard');
    }

    public function pemilihan()
    {
        return view('auth/pemilihan');
    }
}
