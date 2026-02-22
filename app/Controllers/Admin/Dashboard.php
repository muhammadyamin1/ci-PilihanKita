<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $adminId = session()->get('id');

        $kategoriModel = new \App\Models\KategoriModel();
        $calonModel    = new \App\Models\CalonModel();
        $suaraModel    = new \App\Models\SuaraModel();
        $pemilihModel  = new \App\Models\PemilihModel();

        // 1️⃣ Ambil kategori aktif milik admin
        $kategoriAktif = $kategoriModel
            ->where('admin_id', $adminId)
            ->where('aktif', 1)
            ->first();

        if (!$kategoriAktif) {
            return view('admin/dashboard', [
                'kategori' => null
            ]);
        }

        // 2️⃣ Ambil hasil suara per calon (berdasarkan kategori aktif)
        $hasil = $calonModel
            ->select('calon.id, calon.nama_calon, COUNT(suara.id) as total_suara')
            ->join('suara', 'suara.calon_id = calon.id', 'left')
            ->where('calon.kategori_id', $kategoriAktif['id'])
            ->groupBy('calon.id')
            ->findAll();

        // 3️⃣ Total Calon
        $totalCalon = $calonModel
            ->where('kategori_id', $kategoriAktif['id'])
            ->countAllResults();

        // 4️⃣ Suara Masuk (JOIN ke calon supaya tidak campur kategori lain)
        $suaraMasuk = $suaraModel
            ->join('calon', 'calon.id = suara.calon_id')
            ->where('calon.kategori_id', $kategoriAktif['id'])
            ->countAllResults();

        // 5️⃣ Total Pemilih kategori ini
        $totalPemilih = $pemilihModel
            ->where('role', 'user')
            ->where('kategori_id', $kategoriAktif['id'])
            ->countAllResults();

        // 6️⃣ Partisipasi
        $partisipasi = $totalPemilih > 0
            ? round(($suaraMasuk / $totalPemilih) * 100, 1)
            : 0;

        return view('admin/dashboard', [
            'kategori'      => $kategoriAktif,
            'hasil'         => $hasil,
            'totalCalon'    => $totalCalon,
            'totalPemilih'  => $totalPemilih,
            'suaraMasuk'    => $suaraMasuk,
            'partisipasi'   => $partisipasi
        ]);
    }
}
