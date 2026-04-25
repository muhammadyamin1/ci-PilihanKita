<?php

namespace App\Models;

use CodeIgniter\Model;

class SuaraModel extends Model
{
    protected $table            = 'suara';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'calon_id',
        'waktu_pilih'
    ];

    protected $useTimestamps = false;
}