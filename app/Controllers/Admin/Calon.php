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

        if (!$foto || !$foto->isValid()) {
            return $this->response->setJSON(['success' => false, 'error' => 'File foto gagal diupload']);
        }

        // Validasi tipe file: hanya JPEG/PNG
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($foto->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'error' => 'File foto harus berupa gambar (JPEG/PNG)']);
        }

        // Validasi ukuran file: maksimal 2MB
        if ($foto->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ukuran file maksimal 2MB']);
        }

        $namaCalon = trim($this->request->getPost('nama_calon'));
        $wakilCalon = trim($this->request->getPost('wakil_calon') ?? '');
        $visi = trim($this->request->getPost('visi') ?? '');
        $misi = trim($this->request->getPost('misi') ?? '');
        $kategoriId = $this->request->getPost('kategori_id');

        // Validasi: nama calon wajib diisi
        if (empty($namaCalon)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Nama calon wajib diisi']);
        }

        // Validasi: kategori wajib dipilih
        if (empty($kategoriId)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Kategori pemilihan wajib dipilih']);
        }

        // Validasi: kategori harus milik admin yang login (cegah IDOR)
        $kategori = $this->kategoriModel->where('id', $kategoriId)->where('admin_id', session()->get('id'))->first();
        if (!$kategori) {
            return $this->response->setJSON(['success' => false, 'error' => 'Kategori pemilihan tidak valid']);
        }

        // Batasi panjang input untuk mencegah penyalahgunaan
        $namaCalon = mb_substr($namaCalon, 0, 100);
        $wakilCalon = mb_substr($wakilCalon, 0, 100);
        $visi = mb_substr($visi, 0, 1000);
        $misi = mb_substr($misi, 0, 1000);

        // Gabungkan nama calon & wakil
        $combinedName = $namaCalon . '_' . $wakilCalon;

        // Ganti semua karakter non-alfanumerik dengan underscore
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', $combinedName);

        // Ganti beberapa underscore berturut-turut jadi satu
        $cleanName = preg_replace('/_+/', '_', $cleanName);

        // Hilangkan underscore di awal dan akhir
        $cleanName = trim($cleanName, '_');

        // Jika bersih semua (misal input kosong), beri nama default
        if (empty($cleanName)) {
            $cleanName = 'calon_' . time();
        }

        // Lowercase dan tambah ekstensi
        $filename = strtolower($cleanName) . '.jpg';
        $path = 'uploads/calon/' . $filename;

        // Pastikan folder tujuan ada
        if (!is_dir(WRITEPATH . 'uploads/calon')) {
            mkdir(WRITEPATH . 'uploads/calon', 0777, true);
        }
        $foto->move(WRITEPATH . 'uploads/calon', $filename, true);

        // Sanitasi visi/misi dari tag HTML berbahaya
        $visiClean = $visi !== '' ? strip_tags($visi, '<br><p><ul><ol><li><strong><em><b><i><u>') : null;
        $misiClean = $misi !== '' ? strip_tags($misi, '<br><p><ul><ol><li><strong><em><b><i><u>') : null;

        $this->calonModel->insert([
            'admin_id' => session()->get('id'),
            'nama_calon' => $namaCalon,
            'wakil_calon' => $wakilCalon,
            'visi'        => $visiClean,
            'misi'        => $misiClean,
            'kategori_id' => $kategoriId,
            'foto' => $path
        ]);

        $id = $this->calonModel->getInsertID();

        return $this->response->setJSON([
            'success' => true,
            'newCalon' => [
                'id'           => $id,
                'nama_calon'   => $namaCalon,
                'wakil_calon'  => $wakilCalon,
                'visi'         => $visiClean,
                'misi'         => $misiClean,
                'kategori'     => $kategori['nama'] ?? '',
                'foto'         => base_url('foto/calon/' . basename($path)),
            ]
        ]);
    }

    public function get($id)
    {
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID tidak valid']);
        }

        $calon = $this->calonModel
            ->select('calon.*, kategori_pemilihan.nama as kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = calon.kategori_id', 'left')
            ->where('calon.id', $id)
            ->where('calon.admin_id', session()->get('id')) // Pastikan milik admin yang login
            ->first();

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

        // Ambil data lama & validasi kepemilikan (cegah IDOR)
        $oldData = $this->calonModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$oldData) {
            return $this->response->setJSON(['success' => false, 'error' => 'Data calon tidak ditemukan atau bukan milik Anda.']);
        }

        $namaCalon = trim($this->request->getPost('nama_calon'));
        $wakilCalon = trim($this->request->getPost('wakil_calon') ?? '');
        $visi = trim($this->request->getPost('visi') ?? '');
        $misi = trim($this->request->getPost('misi') ?? '');
        $kategoriId = $this->request->getPost('kategori_id');

        // Validasi: nama calon wajib diisi
        if (empty($namaCalon)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Nama calon wajib diisi']);
        }

        // Validasi: kategori wajib dipilih
        if (empty($kategoriId)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Kategori pemilihan wajib dipilih']);
        }

        // Validasi: kategori harus milik admin yang login
        $kategori = $this->kategoriModel->where('id', $kategoriId)->where('admin_id', session()->get('id'))->first();
        if (!$kategori) {
            return $this->response->setJSON(['success' => false, 'error' => 'Kategori pemilihan tidak valid']);
        }

        // Batasi panjang input
        $namaCalon = mb_substr($namaCalon, 0, 100);
        $wakilCalon = mb_substr($wakilCalon, 0, 100);
        $visi = mb_substr($visi, 0, 1000);
        $misi = mb_substr($misi, 0, 1000);

        // Sanitasi visi/misi dari tag HTML berbahaya
        $visiClean = $visi !== '' ? strip_tags($visi, '<br><p><ul><ol><li><strong><em><b><i><u>') : null;
        $misiClean = $misi !== '' ? strip_tags($misi, '<br><p><ul><ol><li><strong><em><b><i><u>') : null;

        $data = [
            'nama_calon' => $namaCalon,
            'wakil_calon' => $wakilCalon,
            'visi' => $visiClean,
            'misi' => $misiClean,
            'kategori_id' => $kategoriId,
        ];

        // Jika upload foto baru
        $foto = $this->request->getFile('fotoGabungan');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {

            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($foto->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON(['success' => false, 'error' => 'File foto harus berupa gambar (JPEG/PNG)']);
            }

            // Validasi ukuran file: maksimal 2MB
            if ($foto->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ukuran file maksimal 2MB']);
            }

            // Hapus file lama jika ada sebelum menimpa/mengganti
            if (!empty($oldData['foto'])) {
                $oldFilePath = WRITEPATH . $oldData['foto'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Generate nama file baru
            $combinedName = $namaCalon . '_' . $wakilCalon;
            $cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', $combinedName);
            $cleanName = preg_replace('/_+/', '_', $cleanName);
            $filename = strtolower(trim($cleanName, '_')) . '.jpg';

            // Jika bersih semua, beri nama default
            if (empty(trim($cleanName, '_'))) {
                $filename = 'calon_' . time() . '.jpg';
            }

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
        // Validasi kepemilikan: hanya bisa hapus milik sendiri
        $data = $this->calonModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if ($data) {
            $filePath = WRITEPATH . 'uploads/calon/' . basename($data['foto']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->calonModel->delete($id);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true]);
            }

            return redirect()->back()->with('success', 'Calon berhasil dihapus');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Data calon tidak ditemukan atau bukan milik Anda']);
        }

        return redirect()->back()->with('error', 'Data calon tidak ditemukan atau bukan milik Anda');
    }
}