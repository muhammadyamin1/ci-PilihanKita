<?php

namespace App\Models;

use CodeIgniter\Model;

class PemilihModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $allowedFields = [
        'admin_id',
        'username',
        'password',
        'nama',
        'email',
        'role',
        'generated',
        'sudah_memilih',
    ];

    protected $useTimestamps = false;

    // =========================
    // PAGINATION FOR ADMIN
    // =========================
    public function getPaginatedByAdmin($adminId, $perPage = 20)
    {
        $builder = $this->select('users.*, kategori_pemilihan.nama AS nama_kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = users.kategori_id', 'left')
            ->where('users.role', 'user')
            ->orderBy('users.id', 'DESC');

        // Jika bukan superadmin → filter admin_id
        if (session('role') !== 'superadmin') {
            $builder->where('users.admin_id', $adminId);
        }

        return $builder->paginate($perPage);
    }

    // Untuk kebutuhan lain (export)
    public function getByAdmin($adminId)
    {
        return $this->where('admin_id', $adminId)
            ->where('role', 'user')
            ->findAll();
    }

    // =========================
    // VALIDATION
    // =========================
    protected $validationRules = [
        'username' => 'required|min_length[4]|is_unique[users.username,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'nama'     => 'required|min_length[3]',
        'email'    => 'permit_empty|valid_email',
        'role'     => 'in_list[admin,user,superadmin]',
    ];

    protected $validationMessages = [
        'username' => [
            'required'  => 'Username wajib diisi',
            'is_unique' => 'Username sudah digunakan',
        ],
        'nama' => [
            'required' => 'Nama wajib diisi',
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
        ],
    ];

    protected $skipValidation = false;
}
