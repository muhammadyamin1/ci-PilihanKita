<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CalonModel;

class Cleanup extends BaseController
{
    /**
     * Skrip Pembersihan File Sampah (Orphaned Files)
     * Menghapus file di folder uploads yang datanya sudah tidak ada di database.
     */
    public function index()
    {
        $results = [];

        // 1. Bersihkan Foto User
        $results['user'] = $this->cleanupFolder(
            WRITEPATH . 'uploads/user/',
            new UserModel(),
            'foto'
        );

        // 2. Bersihkan Foto Calon
        $results['calon'] = $this->cleanupFolder(
            WRITEPATH . 'uploads/calon/',
            new CalonModel(),
            'foto'
        );

        return $this->renderResult($results);
    }

    /**
     * Logika pembersihan folder berdasarkan model
     */
    private function cleanupFolder($path, $model, $column)
    {
        if (!is_dir($path)) {
            return ['status' => 'error', 'message' => "Direktori $path tidak ditemukan."];
        }

        $files = array_diff(scandir($path), ['.', '..', '.gitkeep', 'index.html']);
        $deletedCount = 0;
        $deletedFiles = [];
        $keptCount = 0;

        foreach ($files as $file) {
            // Cek apakah nama file ada di database
            $exists = $model->where($column, $file)->first();

            if (!$exists) {
                if (unlink($path . $file)) {
                    $deletedCount++;
                    $deletedFiles[] = $file;
                }
            } else {
                $keptCount++;
            }
        }

        return [
            'status' => 'success',
            'total_files_scanned' => count($files),
            'deleted_count' => $deletedCount,
            'kept_count' => $keptCount,
            'deleted_files' => $deletedFiles
        ];
    }

    private function renderResult($results)
    {
        echo "<h1>Cleanup Report</h1>";
        foreach ($results as $type => $res) {
            echo "<h3>Type: " . ucfirst($type) . "</h3>";
            if ($res['status'] === 'error') {
                echo "<p style='color:red'>" . $res['message'] . "</p>";
            } else {
                echo "<ul>";
                echo "<li>Total file dipindai: " . $res['total_files_scanned'] . "</li>";
                echo "<li>File dihapus (tidak ada di DB): " . $res['deleted_count'] . "</li>";
                echo "<li>File dipertahankan: " . $res['kept_count'] . "</li>";
                echo "</ul>";
                
                if (!empty($res['deleted_files'])) {
                    echo "<p>File yang dihapus: <code>" . implode(', ', $res['deleted_files']) . "</code></p>";
                }
            }
            echo "<hr>";
        }
        echo "<p>Pembersihan selesai.</p>";
    }
}
