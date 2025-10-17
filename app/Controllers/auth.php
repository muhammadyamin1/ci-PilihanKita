<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function login()
    {
        $session = session();
        $userModel = new \App\Models\UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cek username
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Cek password (password_verify)
            if (password_verify($password, $user['password'])) {
                // Set data session
                $sessionData = [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'nama'     => $user['nama'],
                    'role'     => $user['role'],
                    'logged_in' => true,
                ];
                $session->set($sessionData);

                // Arahkan sesuai role
                if ($user['role'] === 'admin') {
                    return redirect()->to('admin/dashboard');
                } else {
                    return redirect()->to('user/pemilihan');
                }
            } else {
                return redirect()->back()->with('error', 'Password salah!');
            }
        } else {
            return redirect()->back()->with('error', 'Username tidak ditemukan!');
        }
    }

    public function pemilihan()
    {
        return view('user/pemilihan');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
