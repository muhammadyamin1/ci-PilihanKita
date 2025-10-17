<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Admin',
            'totalKandidat' => 4,
            'totalPemilih' => 120,
        ];
        return view('admin/dashboard', $data);
    }
}