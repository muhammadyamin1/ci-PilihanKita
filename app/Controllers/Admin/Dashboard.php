<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        // If SuperAdmin, show global summary + top categories
        if ($role === 'superadmin') {
            $db = \Config\Database::connect();
            $kategoriModel = new \App\Models\KategoriModel();
            $calonModel    = new \App\Models\CalonModel();
            $userModel     = new \App\Models\UserModel();
            $suaraModel    = new \App\Models\SuaraModel();

            $totalCategories = $kategoriModel->countAllResults();
            $totalCalon = $calonModel->countAllResults();
            $totalPemilih = $userModel->where('role', 'user')->countAllResults();
            $totalSuara = $suaraModel->countAllResults();
            $avgPartisipasi = $totalPemilih > 0 ? round(($totalSuara / $totalPemilih) * 100, 1) : 0;

            // Top 10 kategori by total suara
            $builder = $db->table('kategori_pemilihan k')
                ->select('k.id, k.nama, u.nama as admin_name, COUNT(DISTINCT c.id) as total_calon, COUNT(s.id) as total_suara')
                ->join('users u', 'u.id = k.admin_id', 'left')
                ->join('calon c', 'c.kategori_id = k.id', 'left')
                ->join('suara s', 's.calon_id = c.id', 'left')
                ->groupBy('k.id, k.nama, u.nama')
                ->orderBy('total_suara', 'DESC')
                ->limit(10);

            $topCategories = $builder->get()->getResultArray();

            return view('admin/superadmin/dashboard', [
                'totalCategories' => $totalCategories,
                'totalCalon' => $totalCalon,
                'totalPemilih' => $totalPemilih,
                'totalSuara' => $totalSuara,
                'avgPartisipasi' => $avgPartisipasi,
                'topCategories' => $topCategories,
            ]);
        }

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
            // Jika tidak ada kategori aktif untuk admin (mis. SuperAdmin), kirim data default supaya view tidak error
            return view('admin/dashboard', [
                'kategori'      => ['nama' => '-'],
                'hasil'         => [],
                'totalCalon'    => 0,
                'totalPemilih'  => 0,
                'suaraMasuk'    => 0,
                'partisipasi'   => 0,
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

    /**
     * Return top categories (already available in index) as JSON for AJAX (optional).
     */
    public function categoriesTop()
    {
        if (session()->get('role') !== 'superadmin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $adminId = $this->request->getGet('admin_id');
        $builder = $db->table('kategori_pemilihan k')
            ->select('k.id, k.nama, u.nama as admin_name, COUNT(DISTINCT c.id) as total_calon, COUNT(s.id) as total_suara')
            ->join('users u', 'u.id = k.admin_id', 'left')
            ->join('calon c', 'c.kategori_id = k.id', 'left')
            ->join('suara s', 's.calon_id = c.id', 'left');

        if (!empty($adminId)) {
            $builder->where('k.admin_id', (int)$adminId);
        }

        $builder->groupBy('k.id, k.nama, u.nama')->orderBy('total_suara', 'DESC')->limit(50);

        $rows = $builder->get()->getResultArray();
        return $this->response->setJSON($rows);
    }

    /**
     * Return category stats (hasil per calon and totals) as JSON
     */
    public function categoryStats($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $calonModel = new \App\Models\CalonModel();
        $kategoriModel = new \App\Models\KategoriModel();

        $hasil = $calonModel
            ->select('calon.id, calon.nama_calon, COUNT(suara.id) as total_suara')
            ->join('suara', 'suara.calon_id = calon.id', 'left')
            ->where('calon.kategori_id', (int)$id)
            ->groupBy('calon.id')
            ->findAll();

        $totalCalon = $calonModel->where('kategori_id', (int)$id)->countAllResults();
        $suaraModel = new \App\Models\SuaraModel();
        $totalSuara = $suaraModel->join('calon', 'calon.id = suara.calon_id')->where('calon.kategori_id', (int)$id)->countAllResults();

        $userModel = new \App\Models\UserModel();
        $totalPemilih = $userModel->where('role', 'user')->where('kategori_id', (int)$id)->countAllResults();

        $partisipasi = $totalPemilih > 0 ? round(($totalSuara / $totalPemilih) * 100, 1) : 0;

        $kategori = $kategoriModel->where('id', (int)$id)->first();

        return $this->response->setJSON([
            'nama' => $kategori['nama'] ?? ('Kategori ' . $id),
            'hasil' => $hasil,
            'totalCalon' => $totalCalon,
            'totalPemilih' => $totalPemilih,
            'suaraMasuk' => $totalSuara,
            'partisipasi' => $partisipasi,
        ]);
    }

    /**
     * Return list of admins (for Select/search)
     */
    public function adminsList()
    {
        if (session()->get('role') !== 'superadmin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $q = $this->request->getGet('q');
        $userModel = new \App\Models\UserModel();

        $builder = $userModel->where('role', 'admin');
        if (!empty($q)) {
            $builder->groupStart()
                ->like('username', $q)
                ->orLike('nama', $q)
                ->groupEnd();
        }

        $rows = $builder->select('id, username, nama')->limit(50)->findAll();

        $out = array_map(function ($r) {
            return ['id' => $r['id'], 'text' => $r['nama'] . ' (' . $r['username'] . ')'];
        }, $rows);

        return $this->response->setJSON($out);
    }
}
