<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KategoriModel;
use App\Models\CalonModel;
use App\Models\SuaraModel;

class SuperAdmin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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

        return redirect()->to('/admin/admins')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/admin/admins')->with('error', 'Admin tidak ditemukan.');
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
            return redirect()->to('/admin/admins')->with('error', 'Admin tidak ditemukan.');
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

        return redirect()->to('/admin/admins')->with('success', 'Admin berhasil diperbarui.');
    }

    public function deleteConfirm($id)
    {
        $admin = $this->userModel->where('id', $id)->where('role', 'admin')->first();

        if (!$admin) {
            return redirect()->to('/admin/admins')->with('error', 'Admin tidak ditemukan.');
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
            return redirect()->to('/admin/admins')->with('error', 'Admin tidak ditemukan.');
        }

        if ($id == session()->get('id')) {
            return redirect()->to('/admin/admins')->with('error', 'Anda tidak dapat menghapus akun admin yang sedang digunakan.');
        }

        $confirmation = trim($this->request->getPost('confirm_delete'));
        if ($confirmation !== 'HAPUS ADMIN') {
            return redirect()->back()->with('error', 'Konfirmasi tidak valid. Ketik HAPUS ADMIN untuk melanjutkan.');
        }

        $this->userModel->delete($id);

        return redirect()->to('/admin/admins')->with('success', 'Admin berhasil dihapus beserta data terkait.');
    }
}
