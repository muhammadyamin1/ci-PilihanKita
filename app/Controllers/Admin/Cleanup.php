<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CalonModel;
use App\Models\KategoriModel;
use App\Models\SuaraModel;
use CodeIgniter\Database\ConnectionInterface;

class Cleanup extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $preview = [
            'user' => $this->getOrphanFiles(WRITEPATH . 'uploads/user/', new UserModel(), 'foto'),
            'calon' => $this->getOrphanFiles(WRITEPATH . 'uploads/calon/', new CalonModel(), 'foto'),
        ];

        $counts = [
            'suara' => $this->db->table('suara')->countAllResults(),
            'users' => (new UserModel())->where('role', 'user')->countAllResults(),
            'calon' => (new CalonModel())->countAllResults(),
            'kategori' => (new KategoriModel())->countAllResults(),
            'admins' => (new UserModel())->where('role', 'admin')->countAllResults(),
        ];

        return view('admin/cleanup', [
            'preview' => $preview,
            'counts' => $counts,
        ]);
    }

    public function delete()
    {
        $orphanUsers = $this->getOrphanFiles(WRITEPATH . 'uploads/user/', new UserModel(), 'foto');
        $orphanCalons = $this->getOrphanFiles(WRITEPATH . 'uploads/calon/', new CalonModel(), 'foto');

        $result = [
            'user' => $this->removeOrphanFiles(WRITEPATH . 'uploads/user/', $orphanUsers['files']),
            'calon' => $this->removeOrphanFiles(WRITEPATH . 'uploads/calon/', $orphanCalons['files']),
        ];

        return redirect()->to('/admin/cleanup')->with('success', 'Hapus file orphan selesai.')->with('cleanup_result', $result);
    }

    public function deleteData()
    {
        $action = $this->request->getPost('action');
        $confirmation = trim($this->request->getPost('confirm_delete'));

        if (empty($action) || empty($confirmation)) {
            return redirect()->back()->with('error', 'Aksi atau konfirmasi tidak boleh kosong.');
        }

        $validations = [
            'delete_votes' => 'HAPUS',
            'delete_users' => 'HAPUS',
            'delete_calon' => 'HAPUS',
            'delete_categories' => 'HAPUS',
            'delete_admins' => 'HAPUS',
            'delete_all' => 'HAPUS SEMUA',
        ];

        if (!isset($validations[$action])) {
            return redirect()->back()->with('error', 'Aksi tidak dikenal.');
        }

        if ($confirmation !== $validations[$action]) {
            return redirect()->back()->with('error', 'Konfirmasi tidak valid.');
        }

        $progress = [];

        try {
            $this->db->transStart();

            switch ($action) {
                case 'delete_votes':
                    $this->db->table('suara')->delete([]);
                    $progress[] = 'Semua suara berhasil dihapus.';
                    break;

                case 'delete_users':
                    (new UserModel())->where('role', 'user')->delete();
                    $progress[] = 'Semua pemilih berhasil dihapus.';
                    break;

                case 'delete_calon':
                    $this->deleteAllCalon();
                    $progress[] = 'Semua calon berhasil dihapus.';
                    break;

                case 'delete_categories':
                    $remainingCalon = (new CalonModel())->countAllResults();
                    if ($remainingCalon > 0) {
                        return redirect()->back()->with('error', 'Masih ada calon yang terkait dengan kategori. Hapus calon terlebih dahulu.');
                    }
                    (new KategoriModel())->delete([]);
                    $progress[] = 'Semua kategori berhasil dihapus.';
                    break;

                case 'delete_admins':
                    $superadminId = session()->get('id');
                    (new UserModel())
                        ->where('role', 'admin')
                        ->where('id !=', $superadminId)
                        ->delete();
                    $progress[] = 'Semua admin (selain Super Admin) berhasil dihapus.';
                    break;

                case 'delete_all':
                    $this->db->table('suara')->delete([]);
                    $progress[] = 'Semua suara dihapus.';

                    (new UserModel())->where('role', 'user')->delete();
                    $progress[] = 'Semua pemilih dihapus.';

                    $this->deleteAllCalon();
                    $progress[] = 'Semua calon dihapus.';

                    (new KategoriModel())->delete([]);
                    $progress[] = 'Semua kategori dihapus.';

                    $superadminId = session()->get('id');
                    (new UserModel())
                        ->where('role', 'admin')
                        ->where('id !=', $superadminId)
                        ->delete();
                    $progress[] = 'Semua admin selain Super Admin dihapus.';
                    break;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal.');
            }

            return redirect()->to('/admin/cleanup')->with('success', 'Aksi berhasil dijalankan.')->with('cleanup_progress', $progress);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Cleanup deleteData error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menjalankan aksi. ' . $e->getMessage());
        }
    }

    private function getOrphanFiles($path, $model, $column)
    {
        $result = ['files' => []];

        if (!is_dir($path)) {
            return $result;
        }

        $files = array_diff(scandir($path), ['.', '..', '.gitkeep', 'index.html']);
        $dbValues = $model->select($column)->findAll();

        $dbBasenames = [];
        foreach ($dbValues as $row) {
            $value = $row[$column] ?? '';
            if ($value !== '') {
                $dbBasenames[basename($value)] = true;
            }
        }

        foreach ($files as $file) {
            if (!isset($dbBasenames[$file])) {
                $result['files'][] = $file;
            }
        }

        return $result;
    }

    private function removeOrphanFiles($path, array $files)
    {
        $deletedFiles = [];
        $deletedCount = 0;
        $errors = [];

        foreach ($files as $file) {
            $fullPath = $path . $file;
            if (file_exists($fullPath) && is_file($fullPath) && unlink($fullPath)) {
                $deletedFiles[] = $file;
                $deletedCount++;
            } elseif (file_exists($fullPath) && !is_file($fullPath)) {
                $errors[] = "File tidak valid: $file";
            }
        }

        return [
            'status' => empty($errors) ? 'success' : 'error',
            'deleted_count' => $deletedCount,
            'deleted_files' => $deletedFiles,
            'errors' => $errors,
        ];
    }

    private function deleteAllCalon()
    {
        $calonModel = new CalonModel();
        $calons = $calonModel->findAll();

        foreach ($calons as $calon) {
            if (!empty($calon['foto'])) {
                $path = WRITEPATH . $calon['foto'];
                if (file_exists($path) && is_file($path)) {
                    @unlink($path);
                }
            }
        }

        $calonModel->delete([]);
    }
}
