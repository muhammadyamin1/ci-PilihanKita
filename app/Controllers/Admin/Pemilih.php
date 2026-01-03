<?php

namespace App\Controllers\Pemilih;

use App\Controllers\BaseController;
use App\Models\PemilihModel;

class Pemilih extends BaseController
{
    public function index()
    {
        $model = new PemilihModel();
        $data['pemilih'] = $model->findAll();

        return view('admin/pemilih', $data);
    }
}