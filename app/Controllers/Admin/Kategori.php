<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    public function index()
    {
        $model = new KategoriModel();
        $adminId = session()->get('id');
        $data['kategori'] = $model->where('admin_id', $adminId)->findAll();

        return view('admin/kategori', $data);
    }

    public function store()
    {
        $model = new \App\Models\KategoriModel();
        $adminId = session()->get('id');
        $nama = $this->request->getPost('nama');

        $model->insert([
            'nama' => $nama,
            'aktif' => 0,
            'admin_id' => $adminId
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'newId' => $model->getInsertID()
        ]);
    }

    public function toggle($id)
    {
        $model = new KategoriModel();
        $adminId = session()->get('id');
        $model->setActive($id, $adminId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kategori aktif berhasil diubah!'
        ]);
    }

    public function delete($id)
    {
        $model = new \App\Models\KategoriModel();
        $adminId = session()->get('id');

        // Pastikan kategori milik admin ini
        $kategori = $model->where('id', $id)->where('admin_id', $adminId)->first();

        if (!$kategori) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori tidak ditemukan atau bukan milik Anda.'
            ]);
        }

        $model->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!'
        ]);
    }
}
