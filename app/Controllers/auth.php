<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login');
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
                    'id'            => $user['id'],
                    'username'      => $user['username'],
                    'nama'          => $user['nama'],
                    'role'          => $user['role'],
                    'admin_id'      => $user['admin_id'],
                    'kategori_id'   => $user['kategori_id'],
                    'logged_in'     => true,
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
        if (!session()->get('logged_in') || session('role') !== 'user') {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId      = session('id');
        $adminId     = session('admin_id');     // sangat penting
        $kategoriId  = session('kategori_id');

        if (empty($kategoriId)) {
            return redirect()->to('/login')->with('error', 'Kategori pemilihan belum ditentukan untuk akun Anda.');
        }

        // Cek apakah password user adalah password default sistem
        // Jika generated = 1, wajib ubah password dulu
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        if ($user && $user['generated'] == 1) {
            return redirect()->to('/user/ubah-password')->with('warning', 'Anda harus mengubah password default terlebih dahulu sebelum memilih.');
        }

        $calonModel = new \App\Models\CalonModel();

        $calons = $calonModel
            ->where('kategori_id', $kategoriId)
            ->where('admin_id', $adminId)
            ->findAll();

        // Cek apakah user sudah memilih
        $suaraModel = new \App\Models\SuaraModel();
        $sudahMemilih = $suaraModel
            ->where('user_id', $userId)
            ->first() !== null;

        $data = [
            'calons'       => $calons,
            'sudahMemilih' => $sudahMemilih,
            'kategori_nama' => ''   // kita kosongkan dulu, atau ambil manual
        ];

        return view('user/pemilihan', $data);
    }

    /**
     * Proses Voting dari User
     */
    public function vote()
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in') || session('role') !== 'user') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu.'
            ]);
        }

        $userId     = session('id');
        $calonId    = $this->request->getPost('calon_id');
        $kategoriId = session('kategori_id');
        $adminId    = session('admin_id');

        if (empty($calonId) || empty($kategoriId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap.'
            ]);
        }

        $calonModel = new \App\Models\CalonModel();
        $suaraModel = new \App\Models\SuaraModel();   // Pastikan model ini sudah ada

        // Cek apakah calon valid dan sesuai kategori + admin
        $calon = $calonModel
            ->where('id', $calonId)
            ->where('kategori_id', $kategoriId)
            ->where('admin_id', $adminId)
            ->first();

        if (!$calon) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Calon tidak valid.'
            ]);
        }

        // Cek apakah user sudah pernah memilih
        $sudahMemilih = $suaraModel
            ->where('user_id', $userId)
            ->first();

        if ($sudahMemilih) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda sudah memberikan suara.'
            ]);
        }

        // Simpan suara
        try {
            $suaraModel->insert([
                'user_id'  => $userId,
                'calon_id' => $calonId,
                'waktu_pilih' => date('Y-m-d H:i:s')
            ]);

            // Update status sudah_memilih di tabel users
            $userModel = new \App\Models\UserModel();
            $userModel->update($userId, ['sudah_memilih' => 1]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Terima kasih! Suara Anda telah tercatat.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Vote Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan suara.'
            ]);
        }
    }

    public function logout()
    {
        $session = session();
        $session->remove(['id', 'username', 'nama', 'role', 'admin_id', 'kategori_id', 'logged_in']);

        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }

    /**
     * Tampilkan form ubah password (untuk user dengan password sistem)
     */
    public function ubahPassword()
    {
        if (!session()->get('logged_in') || session('role') !== 'user') {
            return redirect()->to('/login');
        }

        $userId = session('id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Jika bukan password sistem, redirect ke pemilihan
        if (!$user || $user['generated'] != 1) {
            return redirect()->to('user/pemilihan');
        }

        return view('user/ubah_password');
    }

    /**
     * Proses ubah password
     */
    public function processUbahPassword()
    {
        if (!session()->get('logged_in') || session('role') !== 'user') {
            return redirect()->to('/login');
        }

        $userId = session('id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Jika bukan password sistem, redirect ke pemilihan
        if (!$user || $user['generated'] != 1) {
            return redirect()->to('user/pemilihan');
        }

        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasiPassword = $this->request->getPost('konfirmasi_password');

        // Validasi
        if (strlen($passwordBaru) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        if (!preg_match('/[A-Za-z]/', $passwordBaru) || !preg_match('/[0-9]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Password harus mengandung huruf dan angka.');
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        // Update password dan set generated = 0
        $userModel->update($userId, [
            'password'   => password_hash($passwordBaru, PASSWORD_DEFAULT),
            'generated'  => 0
        ]);

        return redirect()->to('user/pemilihan')->with('success', 'Password berhasil diubah. Sekarang Anda bisa memilih.');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function processForgotPassword()
    {
        $userModel = new \App\Models\UserModel();

        $username = $this->request->getPost('username');

        $user = $userModel
            ->where('username', $username)
            ->first();

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Username tidak ditemukan');
        }

        if (empty($user['email'])) {
            return redirect()->back()
                ->with('error', 'Email belum terdaftar. Silakan hubungi admin.');
        }

        // tahap awal: belum kirim email
        return redirect()->back()
            ->with('success', 'Permintaan reset diterima. Silakan cek email Anda.');
    }
}
