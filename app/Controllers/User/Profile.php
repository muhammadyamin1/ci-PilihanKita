<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session('id');
        
        $user = $userModel->find($userId);
        
        $data = [
            'title' => 'Profile',
            'user' => $user
        ];
        
        return view('user/profile', $data);
    }
    
    public function updatePassword()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session('id');
        
        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasiPassword = $this->request->getPost('konfirmasi_password');
        
        // Validasi input
        if (empty($passwordLama) || empty($passwordBaru) || empty($konfirmasiPassword)) {
            return redirect()->back()->with('error', 'Semua field harus diisi.');
        }
        
        // Ambil user
        $user = $userModel->find($userId);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }
        
        // Verifikasi password lama
        if (!password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }
        
        // Validasi password baru
        if (strlen($passwordBaru) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }
        
        if (!preg_match('/[A-Za-z]/', $passwordBaru) || !preg_match('/[0-9]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Password harus mengandung huruf dan angka.');
        }
        
        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }
        
        // Update password
        $userModel->update($userId, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);
        
        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }
}