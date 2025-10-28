<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CalonModel;
use App\Models\KategoriModel;

class Calon extends BaseController
{
    protected $calonModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->calonModel = new CalonModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $data['calon'] = $this->calonModel
            ->select('calon.*, kategori_pemilihan.nama as kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = calon.kategori_id')
            ->where('calon.admin_id', session()->get('id'))
            ->findAll();
        $data['kategori'] = $this->kategoriModel
            ->where('admin_id', session()->get('id'))
            ->findAll();

        return view('admin/calon', $data);
    }

    public function save()
    {
        $foto = $this->request->getFile('fotoGabungan');

        if (!$foto->isValid()) {
            return $this->response->setJSON(['success' => false, 'error' => 'File gagal diupload']);
        }

        $namaCalon = $this->request->getPost('nama_calon');
        $wakilCalon = $this->request->getPost('wakil_calon');
        $visi = $this->request->getPost('visi');
        $misi = $this->request->getPost('misi');

        // Gabungkan nama calon & wakil
        $combinedName = $namaCalon . '_' . $wakilCalon;

        // Ganti semua karakter non-alfanumerik dengan underscore
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', $combinedName);

        // Ganti beberapa underscore berturut-turut jadi satu
        $cleanName = preg_replace('/_+/', '_', $cleanName);

        // Hilangkan underscore di awal dan akhir
        $cleanName = trim($cleanName, '_');

        // Lowercase dan tambah ekstensi
        $filename = strtolower($cleanName) . '.jpg';
        $path = 'uploads/calon/' . $filename;

        // Pastikan folder tujuan ada
        if (!is_dir(WRITEPATH . 'uploads/calon')) {
            mkdir(WRITEPATH . 'uploads/calon', 0777, true);
        }
        $foto->move(WRITEPATH . 'uploads/calon', $filename, true);

        $this->calonModel->insert([
            'admin_id' => session()->get('id'),
            'nama_calon' => $namaCalon,
            'wakil_calon' => $wakilCalon,
            'visi'        => $visi !== '' ? $visi : null,
            'misi'        => $misi !== '' ? $misi : null,
            'kategori_id' => $this->request->getPost('kategori_id'),
            'foto' => $path
        ]);

        $id = $this->calonModel->getInsertID();
        $kategoriId = $this->request->getPost('kategori_id');

        $kategoriModel = new \App\Models\KategoriModel();
        $kategori = $kategoriModel->find($kategoriId);

        return $this->response->setJSON([
            'success' => true,
            'newCalon' => [
                'id'           => $id,
                'nama_calon'   => $namaCalon,
                'wakil_calon'  => $wakilCalon,
                'visi'         => $visi,
                'misi'         => $misi,
                'kategori'     => $kategori['nama'] ?? '',
                'foto'         => base_url('foto/calon/' . basename($path)),
            ]
        ]);
    }

    public function get($id)
    {
        $calon = $this->calonModel
            ->select('calon.*, kategori_pemilihan.nama as kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = calon.kategori_id', 'left')
            ->find($id);

        if (!$calon) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $calon['foto'] = base_url('foto/calon/' . basename($calon['foto']));
        return $this->response->setJSON(['success' => true, 'data' => $calon]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'error' => 'ID calon tidak ditemukan.']);
        }
        $namaCalon = $this->request->getPost('nama_calon');
        $wakilCalon = $this->request->getPost('wakil_calon');
        $visi = $this->request->getPost('visi');
        $misi = $this->request->getPost('misi');
        $kategoriId = $this->request->getPost('kategori_id');

        $data = [
            'nama_calon' => $namaCalon,
            'wakil_calon' => $wakilCalon,
            'visi' => $visi,
            'misi' => $misi,
            'kategori_id' => $kategoriId,
        ];

        // Jika upload foto baru
        $foto = $this->request->getFile('fotoGabungan');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $namaCalon . '_' . $wakilCalon)) . '.jpg';
            $path = 'uploads/calon/' . $filename;

            if (!is_dir(WRITEPATH . 'uploads/calon')) {
                mkdir(WRITEPATH . 'uploads/calon', 0777, true);
            }
            $foto->move(WRITEPATH . 'uploads/calon', $filename, true);
            $data['foto'] = $path;
        }

        $this->calonModel->update($id, $data);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $data = $this->calonModel->find($id);
        if ($data) {
            $filePath = WRITEPATH . 'uploads/calon/' . basename($data['foto']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->calonModel->delete($id);
            return redirect()->back()->with('success', 'Calon berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Data calon tidak ditemukan');
    }
}
