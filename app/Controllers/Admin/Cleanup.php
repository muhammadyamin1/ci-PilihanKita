<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CalonModel;
use App\Models\KategoriModel;
use App\Models\SuaraModel;
use App\Models\AdminActivityLogModel;
use CodeIgniter\Database\ConnectionInterface;

class Cleanup extends BaseController
{
    protected $db;
    protected $logModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->logModel = new AdminActivityLogModel();
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
            'cleanupAllowedStep' => $this->getCurrentCleanupStep(),
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
            $redirect = redirect()->back()->with('error', 'Aksi atau konfirmasi tidak boleh kosong.');
            if (!empty($action)) {
                $redirect = $redirect->with('cleanup_open_step', $action);
            }
            return $redirect;
        }

        $validations = [
            'delete_votes' => 'HAPUS',
            'delete_users' => 'HAPUS',
            'delete_calon' => 'HAPUS',
            'delete_categories' => 'HAPUS',
            'delete_admins' => 'HAPUS',
        ];

        if (!isset($validations[$action])) {
            return redirect()->back()->with('error', 'Aksi tidak dikenal.')->with('cleanup_open_step', $action);
        }

        if ($confirmation !== $validations[$action]) {
            return redirect()->back()->with('error', 'Konfirmasi tidak valid.')->with('cleanup_open_step', $action);
        }

        if ($orderError = $this->verifyCleanupOrder($action)) {
            return redirect()->back()->with('error', 'Urutan cleanup tidak benar. ' . $orderError)->with('cleanup_open_step', $action);
        }

        // Re-authentication for sensitive actions (require superadmin password)
        $sensitiveActions = ['delete_admins', 'delete_users', 'delete_votes'];
        if (in_array($action, $sensitiveActions, true)) {
            $adminPassword = $this->request->getPost('admin_password');
            if (empty($adminPassword)) {
                return redirect()->back()->with('error', 'Masukkan kata sandi Super Admin untuk melanjutkan.')->with('cleanup_open_step', $action);
            }

            $currentUser = (new UserModel())->find(session()->get('id'));
            if (empty($currentUser) || !password_verify($adminPassword, $currentUser['password'])) {
                return redirect()->back()->with('error', 'Kata sandi tidak cocok. Aksi dibatalkan.')->with('cleanup_open_step', $action);
            }
        }

        $progress = [];

        try {
            $this->db->transStart();

            switch ($action) {
                case 'delete_votes':
                    $this->db->table('suara')->where('1 = 1')->delete();
                    $progress[] = 'Semua suara berhasil dihapus.';
                    break;

                case 'delete_users':
                    $users = (new UserModel())->select('foto')->where('role', 'user')->findAll();
                    $this->deleteUserPhotos($users);
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
                        return redirect()->back()->with('error', 'Masih ada calon yang terkait dengan kategori. Hapus calon terlebih dahulu.')->with('cleanup_open_step', $action);
                    }
                    $this->db->table('kategori_pemilihan')->where('1 = 1')->delete();
                    $progress[] = 'Semua kategori berhasil dihapus.';
                    break;

                case 'delete_admins':
                    $superadminId = session()->get('id');
                    $admins = (new UserModel())
                        ->select('id, foto')
                        ->where('role', 'admin')
                        ->where('id !=', $superadminId)
                        ->findAll();
                    $adminIds = array_column($admins, 'id');

                    if (!empty($adminIds)) {
                        $this->deleteCalonByAdminIds($adminIds);
                        $this->deleteUserPhotos((new UserModel())->select('foto')->where('role', 'user')->whereIn('admin_id', $adminIds)->findAll());
                        $this->deleteUserPhotos($admins);
                        (new UserModel())
                            ->where('role', 'admin')
                            ->where('id !=', $superadminId)
                            ->delete();
                    }

                    $progress[] = 'Semua admin berhasil dihapus.';
                    break;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal.');
            }

            // Log activity
            try {
                $this->logModel->logActivity(
                    session()->get('id'),
                    'CLEANUP',
                    'CLEANUP',
                    null,
                    'Menjalankan aksi cleanup: ' . $action,
                    null,
                    $progress
                );
            } catch (\Exception $e) {
                // Logging failure should not block the user; just record in error log
                log_message('error', 'Failed to write cleanup audit log: ' . $e->getMessage());
            }

            return redirect()->to('/admin/cleanup')
                ->with('success', 'Aksi berhasil dijalankan.')
                ->with('cleanup_progress', $progress)
                ->with('cleanup_open_step', $this->getNextCleanupStep($action));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Cleanup deleteData error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menjalankan aksi. ' . $e->getMessage())
                ->with('cleanup_open_step', $action);
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

        $total = count($files);
        foreach ($files as $file) {
            $fullPath = $path . $file;
            if (file_exists($fullPath) && is_file($fullPath) && @unlink($fullPath)) {
                $deletedFiles[] = $file;
                $deletedCount++;
            } elseif (file_exists($fullPath) && !is_file($fullPath)) {
                $errors[] = "File tidak valid: $file";
            }
        }

        $kept = max(0, $total - $deletedCount);

        return [
            'status' => empty($errors) ? 'success' : 'error',
            'total_files_scanned' => $total,
            'deleted_count' => $deletedCount,
            'kept_count' => $kept,
            'deleted_files' => $deletedFiles,
            'errors' => $errors,
        ];
    }

    private function deleteAllCalon()
    {
        $calonModel = new CalonModel();
        $calons = $calonModel->select('id, foto')->findAll();

        $calonIds = [];
        foreach ($calons as $calon) {
            $calonIds[] = $calon['id'];
            if (!empty($calon['foto'])) {
                $path = WRITEPATH . $calon['foto'];
                if (file_exists($path) && is_file($path)) {
                    @unlink($path);
                }
            }
        }

        if (!empty($calonIds)) {
            $this->db->table('suara')->whereIn('calon_id', $calonIds)->delete();
            $calonModel->delete($calonIds);
        }
    }

    private function deleteUserPhotos(array $users)
    {
        foreach ($users as $user) {
            if (!empty($user['foto'])) {
                $path = WRITEPATH . 'uploads/user/' . basename($user['foto']);
                if (file_exists($path) && is_file($path)) {
                    @unlink($path);
                }
            }
        }
    }

    private function deleteCalonByAdminIds(array $adminIds)
    {
        if (empty($adminIds)) {
            return;
        }

        $calonModel = new CalonModel();
        $calons = $calonModel->select('id, foto')->whereIn('admin_id', $adminIds)->findAll();

        $calonIds = [];
        foreach ($calons as $calon) {
            $calonIds[] = $calon['id'];
            if (!empty($calon['foto'])) {
                $path = WRITEPATH . $calon['foto'];
                if (file_exists($path) && is_file($path)) {
                    @unlink($path);
                }
            }
        }

        if (!empty($calonIds)) {
            $this->db->table('suara')->whereIn('calon_id', $calonIds)->delete();
            $calonModel->whereIn('id', $calonIds)->delete();
        }
    }

    private function getNextCleanupStep(string $currentAction): string
    {
        $order = [
            'delete_votes',
            'delete_users',
            'delete_calon',
            'delete_categories',
            'delete_admins',
        ];

        $currentIndex = array_search($currentAction, $order, true);
        if ($currentIndex === false || $currentIndex === count($order) - 1) {
            return $currentAction;
        }

        return $order[$currentIndex + 1];
    }

    private function verifyCleanupOrder(string $action): ?string
    {
        $suaraCount = $this->db->table('suara')->countAllResults();
        $userCount = (new UserModel())->where('role', 'user')->countAllResults();
        $calonCount = (new CalonModel())->countAllResults();
        $kategoriCount = (new KategoriModel())->countAllResults();

        switch ($action) {
            case 'delete_votes':
                return null;
            case 'delete_users':
                if ($suaraCount > 0) {
                    return 'Hapus suara terlebih dahulu sebelum menghapus pemilih.';
                }
                return null;
            case 'delete_calon':
                if ($suaraCount > 0 || $userCount > 0) {
                    return 'Hapus suara dan pemilih terlebih dahulu sebelum menghapus calon.';
                }
                return null;
            case 'delete_categories':
                if ($suaraCount > 0 || $userCount > 0 || $calonCount > 0) {
                    return 'Hapus suara, pemilih, dan calon terlebih dahulu sebelum menghapus kategori.';
                }
                return null;
            case 'delete_admins':
                if ($suaraCount > 0 || $userCount > 0 || $calonCount > 0 || $kategoriCount > 0) {
                    return 'Hapus suara, pemilih, calon, dan kategori terlebih dahulu sebelum menghapus admin.';
                }
                return null;
        }

        return null;
    }

    private function getCurrentCleanupStep(): string
    {
        if ($this->db->table('suara')->countAllResults() > 0) {
            return 'delete_votes';
        }
        if ((new UserModel())->where('role', 'user')->countAllResults() > 0) {
            return 'delete_users';
        }
        if ((new CalonModel())->countAllResults() > 0) {
            return 'delete_calon';
        }
        if ((new KategoriModel())->countAllResults() > 0) {
            return 'delete_categories';
        }

        $superadminId = session()->get('id');
        if ((new UserModel())->where('role', 'admin')->where('id !=', $superadminId)->countAllResults() > 0) {
            return 'delete_admins';
        }

        return '';
    }
}
