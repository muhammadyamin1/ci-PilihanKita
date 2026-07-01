<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KategoriModel;
use App\Models\CalonModel;
use App\Models\SuaraModel;
use App\Models\AdminActivityLogModel;

class SuperAdmin extends BaseController
{
    protected $userModel;
    protected $db;
    protected $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel = new AdminActivityLogModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $admins = $this->userModel->where('role', 'admin')->findAll();

        return view('admin/superadmin/admins', [
            'admins' => $admins,
        ]);
    }

    public function create()
    {
        return view('admin/superadmin/admin_form', [
            'admin' => null,
            'title' => 'Tambah Admin',
            'action' => 'store',
        ]);
    }

    public function store()
    {
        $username = trim($this->request->getPost('username'));
        $nama = trim($this->request->getPost('nama'));
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $errors = [];

        if (empty($nama) || mb_strlen($nama) < 3) {
            $errors[] = 'Nama minimal 3 karakter.';
        }

        if (empty($username) || mb_strlen($username) < 4) {
            $errors[] = 'Username minimal 4 karakter.';
        } elseif ($this->userModel->where('username', $username)->first()) {
            $errors[] = 'Username sudah digunakan.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (empty($password) || mb_strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung huruf dan angka.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $errors));
        }

        $this->userModel->insert([
            'admin_id'    => null,
            'username'    => $username,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'nama'        => $nama,
            'email'       => $email !== '' ? $email : null,
            'role'        => 'admin',
            'kategori_id' => null,
            'generated'   => 0,
            'sudah_memilih' => 0,
        ]);

        return redirect()->to('/superadmin/admins')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/superadmin/admins')->with('error', 'Admin tidak ditemukan.');
        }

        return view('admin/superadmin/admin_form', [
            'admin' => $admin,
            'title' => 'Edit Admin',
            'action' => 'update/' . $id,
        ]);
    }

    public function update($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/superadmin/admins')->with('error', 'Admin tidak ditemukan.');
        }

        $username = trim($this->request->getPost('username'));
        $nama = trim($this->request->getPost('nama'));
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $errors = [];

        if (empty($nama) || mb_strlen($nama) < 3) {
            $errors[] = 'Nama minimal 3 karakter.';
        }

        if (empty($username) || mb_strlen($username) < 4) {
            $errors[] = 'Username minimal 4 karakter.';
        } else {
            $exists = $this->userModel->where('username', $username)->where('id !=', $id)->first();
            if ($exists) {
                $errors[] = 'Username sudah digunakan.';
            }
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (!empty($password)) {
            if (mb_strlen($password) < 8) {
                $errors[] = 'Password minimal 8 karakter.';
            } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $errors[] = 'Password harus mengandung huruf dan angka.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Konfirmasi password tidak cocok.';
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $errors));
        }

        $updateData = [
            'username' => $username,
            'nama' => $nama,
            'email' => $email !== '' ? $email : null,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        return redirect()->to('/superadmin/admins')->with('success', 'Admin berhasil diperbarui.');
    }

    public function deleteConfirm($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/superadmin/admins')->with('error', 'Admin tidak ditemukan.');
        }

        $kategoriModel = new KategoriModel();
        $calonModel = new CalonModel();
        $suaraModel = new SuaraModel();

        $kategoriCount = $kategoriModel->where('admin_id', $id)->countAllResults();
        $calonCount = $calonModel->where('admin_id', $id)->countAllResults();
        $pemilihCount = $this->userModel->where('admin_id', $id)->where('role', 'user')->countAllResults();
        $suaraCount = $suaraModel
            ->join('calon', 'calon.id = suara.calon_id')
            ->where('calon.admin_id', $id)
            ->countAllResults();

        return view('admin/superadmin/admin_delete_confirm', [
            'admin' => $admin,
            'kategoriCount' => $kategoriCount,
            'calonCount' => $calonCount,
            'pemilihCount' => $pemilihCount,
            'suaraCount' => $suaraCount,
        ]);
    }

    public function delete($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/superadmin/admins')->with('error', 'Admin tidak ditemukan.');
        }

        if ($id == session()->get('id')) {
            return redirect()->to('/superadmin/admins')->with('error', 'Anda tidak dapat menghapus akun admin yang sedang digunakan.');
        }

        // Protect specific superadmin account (id 2) from deletion
        if ((int) $id === 2) {
            return redirect()->to('/superadmin/admins')->with('error', 'Akun Super Admin dilindungi dan tidak boleh dihapus.');
        }

        $confirmation = trim($this->request->getPost('confirm_delete'));
        if ($confirmation !== 'HAPUS ADMIN') {
            return redirect()->back()->with('error', 'Konfirmasi tidak valid. Ketik HAPUS ADMIN untuk melanjutkan.');
        }

        // Hapus admin dan data terkait secara transactional
        $kategoriModel = new KategoriModel();
        $calonModel = new CalonModel();

        try {
            $this->db->transStart();

            // Hapus foto calon dan suara terkait
            $calons = $calonModel->where('admin_id', $id)->findAll();
            $calonIds = [];
            foreach ($calons as $calon) {
                $calonIds[] = $calon['id'];
                if (!empty($calon['foto'])) {
                    $path = WRITEPATH . $calon['foto'];
                    if (file_exists($path) && is_file($path)) {
                        @unlink($path);
                    }
                }
            }

            if (!empty($calonIds)) {
                // Hapus suara yang terkait calon-calon ini
                $this->db->table('suara')->whereIn('calon_id', $calonIds)->delete();
            }

            // Hapus calon
            $calonModel->where('admin_id', $id)->delete();

            // Hapus kategori milik admin
            $kategoriModel->where('admin_id', $id)->delete();

            // Hapus pemilih (users) yang di-manage admin ini
            $this->userModel->where('admin_id', $id)->where('role', 'user')->delete();

            // Hapus admin
            $this->userModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal saat menghapus admin.');
            }

            // Catat log activity setelah transaksi berhasil
            // Log ini dijaga dan tidak akan dihapus bersama data voting
            $this->logModel->logActivity(
                session()->get('id'),           // admin_id yang melakukan penghapusan (superadmin)
                'DELETE_ADMIN',                 // action
                'ADMIN',                        // target_type
                $id,                            // target_id (admin yang dihapus)
                'Menghapus admin: ' . $admin['nama'] . ' (username: ' . $admin['username'] . ') beserta semua data voting terkait.',
                json_encode($admin),            // old_value (data admin yang dihapus)
                null                            // new_value
            );

            return redirect()->to('/superadmin/admins')->with('success', 'Admin berhasil dihapus beserta data voting terkait. Aktivitas telah dicatat dalam log.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'SuperAdmin delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus admin. ' . $e->getMessage());
        }
    }

    /**
     * Hapus semua data yang terkait dengan admin tertentu, tetapi pertahankan akun admin.
     */
    public function deleteRelatedData($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/superadmin/admins')->with('error', 'Admin tidak ditemukan.');
        }

        // Protect specific superadmin account (id 2) from data deletion
        if ((int) $id === 2) {
            return redirect()->to('/superadmin/admins')->with('error', 'Data untuk akun Super Admin dilindungi dan tidak boleh dihapus.');
        }

        $confirmation = trim($this->request->getPost('confirm_delete'));
        if ($confirmation !== 'HAPUS DATA') {
            return redirect()->back()->with('error', 'Konfirmasi tidak valid. Ketik HAPUS DATA untuk melanjutkan.');
        }

        $kategoriModel = new KategoriModel();
        $calonModel = new CalonModel();

        try {
            $this->db->transStart();

            // Hapus foto calon dan suara terkait
            $calons = $calonModel->where('admin_id', $id)->findAll();
            $calonIds = [];
            foreach ($calons as $calon) {
                $calonIds[] = $calon['id'];
                if (!empty($calon['foto'])) {
                    $path = WRITEPATH . $calon['foto'];
                    if (file_exists($path) && is_file($path)) {
                        @unlink($path);
                    }
                }
            }

            if (!empty($calonIds)) {
                $this->db->table('suara')->whereIn('calon_id', $calonIds)->delete();
            }

            // Hapus calon
            $calonModel->where('admin_id', $id)->delete();

            // Hapus kategori milik admin
            $kategoriModel->where('admin_id', $id)->delete();

            // Hapus pemilih (users) yang di-manage admin ini
            $this->userModel->where('admin_id', $id)->where('role', 'user')->delete();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal saat menghapus data terkait admin.');
            }

            // Catat log activity setelah transaksi berhasil
            $this->logModel->logActivity(
                session()->get('id'),           // admin_id yang melakukan penghapusan (superadmin)
                'DELETE_ADMIN_DATA',            // action
                'ADMIN_DATA',                   // target_type
                $id,                            // target_id (admin yang datanya dihapus)
                'Menghapus semua data voting terkait admin: ' . $admin['nama'] . ' (username: ' . $admin['username'] . '). Admin tetap ada.',
                null,                           // old_value
                null                            // new_value
            );

            return redirect()->to('/superadmin/admins')->with('success', 'Data voting terkait admin berhasil dihapus. Aktivitas telah dicatat dalam log.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'SuperAdmin deleteRelatedData error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data terkait. ' . $e->getMessage());
        }
    }
}
