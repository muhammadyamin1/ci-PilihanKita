<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'username',
        'password',
        'nama',
        'role',
        'sudah_memilih'
    ];
    
    protected $useTimestamps    = true; // otomatis isi created_at kalau ada
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // kosong karena tidak ada kolom updated_at
}