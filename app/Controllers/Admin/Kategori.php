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
        $model = new \App\Models\KategoriModel();
        $adminId = session()->get('id');
        $kategori = $model->where('id', $id)->where('admin_id', $adminId)->first();

        if (!$kategori) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.'
            ]);
        }

        if ($kategori['aktif'] == 1) {
            // Jika kategori ini sedang aktif → matikan
            $model->update($id, ['aktif' => 0]);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori dinonaktifkan.'
            ]);
        } else {
            // Jika kategori ini belum aktif → matikan semua lalu hidupkan yang ini
            $model->where('admin_id', $adminId)->set(['aktif' => 0])->update();
            $model->update($id, ['aktif' => 1]);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori diaktifkan.'
            ]);
        }
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

        try {
            if ($model->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori gagal dihapus.'
                ]);
            }
        } catch (\Exception $e) {
            // Tangani error FK constraint (misal masih ada calon terkait)
            if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                $msg = 'Kategori tidak bisa dihapus karena masih ada calon di dalamnya. Hapus calon tersebut di menu Manajemen Calon terlebih dahulu.';
            } else {
                $msg = 'Terjadi kesalahan saat menghapus kategori: ' . $e->getMessage();
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $msg
            ]);
        }
    }
}
