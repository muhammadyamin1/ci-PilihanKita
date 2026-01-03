<?php

namespace App\Models;
use CodeIgniter\Model;

class PemilihModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'admin_id',
        'username',
        'password',
        'nama',
        'email',
        'role',
        'generated',
        'sudah_memilih',
    ];

    // Timestamp
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = null;

    // =========================
    // VALIDATION
    // =========================
    protected $validationRules = [
        'username' => 'required|min_length[4]|is_unique[users.username,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'nama'     => 'required|min_length[3]',
        'email'    => 'permit_empty|valid_email',
        'role'     => 'in_list[admin,user]',
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