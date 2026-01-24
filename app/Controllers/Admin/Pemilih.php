<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemilihModel;

class Pemilih extends BaseController
{
    public function index()
    {

        return view('admin/pemilih');
    }
}