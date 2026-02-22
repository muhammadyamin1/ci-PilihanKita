<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemilihModel;

class Pemilih extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new PemilihModel();
    }

    public function index()
    {
        $perPage = $this->request->getGet('perPage') ?? 20;

        $allowed = [20, 50, 100];
        if (!in_array((int)$perPage, $allowed)) {
            $perPage = 20;
        }

        $users = $this->model
            ->getPaginatedByAdmin(session('id'), $perPage);

        $data = [
            'users'   => $users,
            'pager'   => $this->model->pager,
            'perPage' => $perPage,
            'total'   => $this->model
                ->where('admin_id', session('id'))
                ->where('role', 'user')
                ->countAllResults()
        ];

        return view('admin/pemilih/index', $data);
    }

    public function create()
    {
        $kategoriModel = new \App\Models\KategoriModel();

        $kategori = $kategoriModel
            ->where('admin_id', session()->get('id'))
            ->findAll();

        return view('admin/pemilih/tambah', [
            'kategori' => $kategori
        ]);
    }

    public function store()
    {
        $userModel = new \App\Models\UserModel();
        $kategoriModel = new \App\Models\KategoriModel();

        $kategori_id = $this->request->getPost('kategori_id');

        // Validasi kategori milik admin
        $cekKategori = $kategoriModel
            ->where('id', $kategori_id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$cekKategori) {
            return redirect()->back()->with('error', 'Kategori tidak valid.');
        }

        $userModel->insert([
            'admin_id'    => session()->get('id'),
            'nama'        => $this->request->getPost('nama'),
            'username'    => $this->request->getPost('username'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => 'user',
            'kategori_id' => $kategori_id
        ]);

        $action = $this->request->getPost('action');

        if ($action == 'save_add') {
            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        }

        return redirect()->to('/admin/pemilih')->with('success', 'User berhasil ditambahkan.');
    }

    public function hapus($id)
    {
        $userModel = new \App\Models\UserModel();

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $userModel->delete($id);

        return redirect()->to('/admin/pemilih')->with('success', 'User berhasil dihapus.');
    }
}
