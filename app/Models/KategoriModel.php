<?php

namespace App\Models;
use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table = 'kategori_pemilihan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'aktif', 'admin_id'];

    public function setActive($id, $adminId)
    {
        // Nonaktifkan semua kategori milik admin ini
        $this->where('admin_id', $adminId)->set(['aktif' => 0])->update();

        // Aktifkan yang dipilih
        $this->update($id, ['aktif' => 1]);
    }
}