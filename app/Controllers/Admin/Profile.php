<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session('id');
        
        $user = $userModel->find($userId);
        
        $data = [
            'title' => 'Profile Admin',
            'user' => $user
        ];
        
        return view('admin/profile', $data);
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
    
    public function updateFoto()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session('id');
        
        $file = $this->request->getFile('foto');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File foto tidak valid.');
        }
        
        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return redirect()->back()->with('error', 'Format foto harus JPG, PNG, GIF, atau WebP.');
        }
        
        // Validasi ukuran (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran foto maksimal 2MB.');
        }
        
        // Hapus foto lama jika ada
        $user = $userModel->find($userId);
        if (!empty($user['foto'])) {
            $fotoLama = WRITEPATH . 'uploads/user/' . $user['foto'];
            if (file_exists($fotoLama)) {
                unlink($fotoLama);
            }
        }
        
        // Generate nama file unik
        $ext = $file->getExtension();
        $namaFile = 'user_' . $userId . '_' . time() . '.' . $ext;
        
        // Buat direktori jika belum ada
        $dir = WRITEPATH . 'uploads/user';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Pindahkan file
        $file->move($dir, $namaFile);
        
        // Update database
        $userModel->update($userId, [
            'foto' => $namaFile
        ]);
        
        return redirect()->back()->with('success', 'Foto profile berhasil diubah.');
    }
}