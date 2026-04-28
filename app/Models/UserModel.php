<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'admin_id',
        'username',
        'password',
        'nama',
        'email',
        'identifier',
        'role',
        'kategori_id',
        'generated',
        'sudah_memilih',
        'foto',
        'verification_code',
        'verification_expires',
        'created_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // tidak ada kolom updated_at
}