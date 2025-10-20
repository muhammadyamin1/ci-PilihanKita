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
        $filename = strtolower(str_replace(' ', '_', $namaCalon . '_' . $wakilCalon)) . '.jpg';
        $path = 'uploads/calon/' . $filename;

        if (!is_dir(FCPATH . 'uploads/calon')) mkdir(FCPATH . 'uploads/calon', 0777, true);
        $foto->move(FCPATH . 'uploads/calon', $filename, true);

        $this->calonModel->insert([
            'admin_id' => session()->get('id'),
            'nama_calon' => $namaCalon,
            'wakil_calon' => $wakilCalon,
            'visi' => $this->request->getPost('visi'),
            'misi' => $this->request->getPost('misi'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'foto' => $path
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $data = $this->calonModel->find($id);
        if ($data && file_exists(FCPATH . $data['foto'])) {
            unlink(FCPATH . $data['foto']);
        }
        $this->calonModel->delete($id);
        return redirect()->back()->with('success', 'Calon berhasil dihapus');
    }
}
