<?php

namespace App\Models;

use CodeIgniter\Model;

class CalonModel extends Model
{
    protected $table = 'calon';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'admin_id',
        'nama_calon',
        'wakil_calon',
        'visi',
        'misi',
        'foto',
        'kategori_id'
    ];
}