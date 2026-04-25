<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemilihModel;

class Pemilih extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new PemilihModel();
    }

    public function index()
    {
        $perPage = $this->request->getGet('perPage') ?? 20;

        $allowed = [20, 50, 100];
        if (!in_array((int)$perPage, $allowed)) {
            $perPage = 20;
        }

        $users = $this->model
            ->getPaginatedByAdmin(session('id'), $perPage);

        $data = [
            'users'   => $users,
            'pager'   => $this->model->pager,
            'perPage' => $perPage,
            'total'   => $this->model
                ->where('admin_id', session('id'))
                ->where('role', 'user')
                ->countAllResults()
        ];

        return view('admin/pemilih/index', $data);
    }

    public function create()
    {
        $kategoriModel = new \App\Models\KategoriModel();

        $kategori = $kategoriModel
            ->where('admin_id', session()->get('id'))
            ->findAll();

        return view('admin/pemilih/tambah', [
            'kategori' => $kategori
        ]);
    }

    public function store()
    {
        $userModel = new \App\Models\UserModel();
        $kategoriModel = new \App\Models\KategoriModel();

        $kategori_id = $this->request->getPost('kategori_id');

        // Validasi kategori milik admin
        $cekKategori = $kategoriModel
            ->where('id', $kategori_id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$cekKategori) {
            return redirect()->back()->with('error', 'Kategori tidak valid.');
        }

        // Validasi manual
        $nama = trim($this->request->getPost('nama'));
        $email = trim($this->request->getPost('email'));
        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        $errors = [];
        if (strlen($nama) < 3) {
            $errors[] = 'Nama minimal 3 karakter.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (strlen($username) < 4) {
            $errors[] = 'Username minimal 4 karakter.';
        } elseif ($userModel->where('username', $username)->first()) {
            $errors[] = 'Username sudah digunakan.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung huruf dan angka.';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $errors));
        }

        $userModel->insert([
            'admin_id'    => session()->get('id'),
            'nama'        => $nama,
            'email'       => $email !== '' ? $email : null,
            'username'    => $username,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'role'        => 'user',
            'kategori_id' => $kategori_id
        ]);

        $action = $this->request->getPost('action');

        if ($action == 'save_add') {
            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        }

        return redirect()->to('/admin/pemilih')->with('success', 'User berhasil ditambahkan.');
    }

    public function hapus($id)
    {
        $userModel = new \App\Models\UserModel();

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $userModel->delete($id);

        return redirect()->to('/admin/pemilih')->with('success', 'User berhasil dihapus.');
    }

    public function generate()
    {
        $kategoriModel = new \App\Models\KategoriModel();

        if ($this->request->getMethod() === 'POST') {
            // Proses generate
            $userModel = new \App\Models\UserModel();

            $kategori_id = $this->request->getPost('kategori_id');
            $jumlah = (int) $this->request->getPost('jumlah');

            // Validasi kategori milik admin
            $cekKategori = $kategoriModel
                ->where('id', $kategori_id)
                ->where('admin_id', session()->get('id'))
                ->first();

            if (!$cekKategori) {
                return redirect()->back()->with('error', 'Kategori tidak valid.');
            }

            if ($jumlah < 1 || $jumlah > 100) {
                return redirect()->back()->with('error', 'Jumlah pemilih harus antara 1-100.');
            }

            // Generate users satu per satu untuk dapat ID
            $users = [];
            $plainPasswords = [];
            $batchId = time(); // Untuk grouping batch

            for ($i = 1; $i <= $jumlah; $i++) {
                $username = 'pemilih_' . $batchId . '_' . $i;
                $plainPassword = 'pass' . rand(1000, 9999); // Random password

                $userId = $userModel->insert([
                    'admin_id' => session()->get('id'),
                    'nama' => 'Pemilih ' . $i,
                    'username' => $username,
                    'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
                    'role' => 'user',
                    'kategori_id' => $kategori_id,
                    'generated' => 1,
                    'sudah_memilih' => 0
                ]);

                $users[] = $userModel->find($userId);
                $plainPasswords[$userId] = $plainPassword;
            }

            // Log ke user_import_log
            $db = \Config\Database::connect();
            $db->table('user_import_log')->insert([
                'admin_id' => session()->get('id'),
                'jumlah_user' => $jumlah,
                'jenis' => 'generate'
            ]);

            // Simpan plain passwords di session untuk batch ini
            session()->set('generated_passwords_' . $batchId, $plainPasswords);

            // Tampilkan halaman generated
            return view('admin/pemilih/generated', [
                'users' => $users,
                'plain_passwords' => $plainPasswords,
                'batch_id' => $batchId
            ]);
        } else {
            // Tampilkan form
            $kategori = $kategoriModel
                ->where('admin_id', session()->get('id'))
                ->findAll();

            return view('admin/pemilih/generate', [
                'kategori' => $kategori
            ]);
        }
    }

    /**
     * Tampilkan form Import Excel/CSV
     */
    public function import()
    {
        $kategoriModel = new \App\Models\KategoriModel();

        $kategori = $kategoriModel
            ->where('admin_id', session()->get('id'))
            ->findAll();

        return view('admin/pemilih/import', [
            'kategori' => $kategori
        ]);
    }

    /**
     * Proses Import CSV/Excel
     */
    public function importProcess()
    {
        $userModel = new \App\Models\UserModel();
        $kategoriModel = new \App\Models\KategoriModel();

        $kategori_id = $this->request->getPost('kategori_id');

        if (!$kategori_id) {
            return redirect()->back()->with('error', 'Kategori harus dipilih.');
        }

        $cekKategori = $kategoriModel
            ->where('id', $kategori_id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$cekKategori) {
            return redirect()->back()->with('error', 'Kategori tidak valid.');
        }

        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau belum dipilih.');
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Hanya file CSV atau Excel (.xlsx, .xls) yang diizinkan.');
        }

        $batchId = time();
        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $plainPasswords = [];   // ← Simpan password plain di sini
        $importedUsers = [];    // ← Simpan data user yang berhasil diimport

        try {
            if ($ext === 'csv') {
                $handle = fopen($file->getTempName(), 'r');
                if (!$handle) {
                    throw new \Exception('Gagal membuka file CSV.');
                }

                // Skip header
                fgetcsv($handle, 0, ';');

                $rowNum = 1;
                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    $rowNum++;

                    if (empty($row[0])) continue;

                    $nama     = trim($row[0] ?? '');
                    $username = trim($row[1] ?? '');
                    $email    = trim($row[2] ?? '');
                    $password = trim($row[3] ?? '');

                    // Validasi dasar
                    if (strlen($nama) < 3) {
                        $errors[] = "Baris $rowNum: Nama minimal 3 karakter.";
                        $failedCount++;
                        continue;
                    }
                    if (strlen($username) < 4) {
                        $errors[] = "Baris $rowNum: Username minimal 4 karakter.";
                        $failedCount++;
                        continue;
                    }
                    if ($userModel->where('username', $username)->first()) {
                        $errors[] = "Baris $rowNum: Username '$username' sudah digunakan.";
                        $failedCount++;
                        continue;
                    }
                    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Baris $rowNum: Format email tidak valid.";
                        $failedCount++;
                        continue;
                    }

                    // Password handling
                    if (empty($password)) {
                        $plainPassword = 'pass' . rand(1000, 9999);
                    } else {
                        $plainPassword = $password;
                        if (
                            strlen($plainPassword) < 8 ||
                            !preg_match('/[A-Za-z]/', $plainPassword) ||
                            !preg_match('/[0-9]/', $plainPassword)
                        ) {
                            $errors[] = "Baris $rowNum: Password harus minimal 8 karakter dan mengandung huruf + angka.";
                            $failedCount++;
                            continue;
                        }
                    }

                    $userId = $userModel->insert([
                        'admin_id'      => session()->get('id'),
                        'nama'          => $nama,
                        'email'         => $email !== '' ? $email : null,
                        'username'      => $username,
                        'password'      => password_hash($plainPassword, PASSWORD_DEFAULT),
                        'role'          => 'user',
                        'kategori_id'   => $kategori_id,
                        'generated'     => 0,
                        'sudah_memilih' => 0
                    ]);

                    // Simpan data untuk ditampilkan nanti
                    $importedUsers[] = [
                        'id'       => $userId,
                        'username' => $username,
                        'nama'     => $nama,
                        'email'    => $email
                    ];
                    $plainPasswords[$userId] = $plainPassword;

                    $successCount++;
                }
                fclose($handle);
            } else {
                return redirect()->back()->with('error', 'Import Excel belum didukung. Gunakan CSV.');
            }

            // Log import
            $db = \Config\Database::connect();
            $db->table('user_import_log')->insert([
                'admin_id'    => session()->get('id'),
                'jumlah_user' => $successCount,
                'jenis'       => 'import'
            ]);

            // Simpan password ke session (sama seperti fitur Generate)
            session()->set('generated_passwords_' . $batchId, $plainPasswords);

            // Jika ada yang gagal, simpan error ke session
            if (!empty($errors)) {
                session()->setFlashdata('import_errors', $errors);
            }

            $message = "$successCount pemilih berhasil diimport.";
            if ($failedCount > 0) {
                $message .= " $failedCount gagal.";
            }

            // **Redirect ke halaman khusus hasil import**
            return redirect()->to('/admin/pemilih/import-result/' . $batchId)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan hasil import + password
     */
    public function importResult($batchId)
    {
        $pemilihModel = new \App\Models\PemilihModel();  // Gunakan PemilihModel

        $users = $pemilihModel
            ->select('users.*, kategori_pemilihan.nama AS nama_kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = users.kategori_id', 'left')
            ->where('users.admin_id', session()->get('id'))
            ->where('users.role', 'user')
            ->where('users.created_at >=', date('Y-m-d H:i:s', $batchId - 10)) // toleransi waktu
            ->orderBy('users.id', 'DESC')
            ->findAll();

        $plainPasswords = session()->get('generated_passwords_' . $batchId);

        if (empty($users) || empty($plainPasswords)) {
            return redirect()->to('/admin/pemilih')
                ->with('error', 'Data hasil import tidak ditemukan atau password sudah tidak tersedia.');
        }

        return view('admin/pemilih/import_result', [
            'users'           => $users,
            'plain_passwords' => $plainPasswords,
            'batch_id'        => $batchId
        ]);
    }

    /**
     * Download CSV hasil import
     */
    public function downloadImportCsv($batchId)
    {
        $pemilihModel = new \App\Models\PemilihModel();

        $users = $pemilihModel
            ->select('users.*, kategori_pemilihan.nama AS nama_kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = users.kategori_id', 'left')
            ->where('users.admin_id', session()->get('id'))
            ->where('users.role', 'user')
            ->where('users.created_at >=', date('Y-m-d H:i:s', $batchId - 10))
            ->orderBy('users.id', 'DESC')
            ->findAll();

        $plainPasswords = session()->get('generated_passwords_' . $batchId);

        if (empty($users) || empty($plainPasswords)) {
            return redirect()->to('/admin/pemilih')
                ->with('error', 'Data hasil import tidak ditemukan.');
        }

        $filename = 'pemilih_import_' . date('Ymd_His', $batchId) . '.csv';

        $output = "Username;Password;Nama;Email;Kategori\n";

        foreach ($users as $user) {
            $password = $plainPasswords[$user['id']] ?? 'N/A';
            $email    = $user['email'] ?? '';
            $kategori = $user['nama_kategori'] ?? '-';
            $nama     = str_replace([';', "\n", "\r"], [' ', ' ', ' '], $user['nama']); // sanitasi

            $output .= $user['username'] . ";" .
                $password . ";" .
                $nama . ";" .
                $email . ";" .
                $kategori . "\n";
        }

        // Hapus session password setelah berhasil download
        session()->remove('generated_passwords_' . $batchId);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo $output;
        exit;
    }

    /**
     * Download Template CSV
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_pemilih.csv';

        $output = "Nama;Username;Email;Password\n";
        $output .= "Contoh Nama;contoh_username;email@contoh.com;pass1234;Kosongkan baris ini!";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo $output;
        exit;
    }

    public function updateNama($id)
    {
        $userModel = new \App\Models\UserModel();

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $nama = $this->request->getPost('nama');
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama tidak boleh kosong.');
        }

        $userModel->update($id, ['nama' => $nama]);

        return redirect()->back()->with('success', 'Nama berhasil diupdate.');
    }

    public function updateAllNames()
    {
        $userModel = new \App\Models\UserModel();

        $namaArray = $this->request->getPost('nama');
        $batchId = $this->request->getPost('batch_id');

        if (empty($namaArray)) {
            return redirect()->back()->with('error', 'Tidak ada data nama untuk diupdate.');
        }

        $updatedCount = 0;
        foreach ($namaArray as $id => $nama) {
            if (empty($nama)) {
                continue; // Skip jika nama kosong
            }

            $user = $userModel
                ->where('id', $id)
                ->where('admin_id', session()->get('id'))
                ->first();

            if ($user) {
                $userModel->update($id, ['nama' => $nama]);
                $updatedCount++;
            }
        }

        // Query ulang users dari batch untuk tampilkan di halaman generated
        $users = $userModel
            ->where('admin_id', session()->get('id'))
            ->like('username', 'pemilih_' . $batchId . '_')
            ->findAll();

        // Ambil plain passwords dari session
        $plainPasswords = session()->get('generated_passwords_' . $batchId);

        // Tampilkan halaman generated lagi dengan pesan sukses
        return view('admin/pemilih/generated', [
            'users' => $users,
            'plain_passwords' => $plainPasswords,
            'batch_id' => $batchId,
            'success_message' => $updatedCount . ' nama berhasil diupdate. Sekarang Anda bisa download CSV dengan nama terbaru.'
        ]);
    }

    public function downloadCsv($batchId)
    {
        $userModel = new \App\Models\UserModel();

        // Ambil users dari batch ini dengan join kategori
        $users = $userModel
            ->select('users.*, kategori_pemilihan.nama AS nama_kategori')
            ->join('kategori_pemilihan', 'kategori_pemilihan.id = users.kategori_id', 'left')
            ->where('users.admin_id', session()->get('id'))
            ->like('users.username', 'pemilih_' . $batchId . '_')
            ->findAll();

        if (empty($users)) {
            return redirect()->to('/admin/pemilih')->with('error', 'Data tidak ditemukan.');
        }

        // Ambil plain passwords dari session
        $plainPasswords = session()->get('generated_passwords_' . $batchId);
        if (!$plainPasswords) {
            return redirect()->to('/admin/pemilih')->with('error', 'Password sudah tidak tersedia. Generate ulang.');
        }

        // Generate CSV dengan semicolon delimiter untuk Excel (karena koma digunakan untuk decimal di Indonesia)
        $filename = 'pemilih_generated_' . $batchId . '.csv';
        $output = "Username;Password;Nama;Kategori\n"; // Header dengan semicolon

        foreach ($users as $user) {
            $password = $plainPasswords[$user['id']] ?? 'N/A';
            $kategori = $user['nama_kategori'] ?? '-';
            $output .= $user['username'] . ";" . $password . ";" . $user['nama'] . ";" . $kategori . "\n";
        }

        // Hapus session setelah download
        session()->remove('generated_passwords_' . $batchId);

        // Set headers untuk download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo $output;
        exit;
    }

    /**
     * Tampilkan form edit pemilih
     */
    public function edit($id)
    {
        $userModel = new \App\Models\UserModel();
        $kategoriModel = new \App\Models\KategoriModel();

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->to('/admin/pemilih')->with('error', 'User tidak ditemukan.');
        }

        $kategori = $kategoriModel
            ->where('admin_id', session()->get('id'))
            ->findAll();

        return view('admin/pemilih/edit', [
            'user' => $user,
            'kategori' => $kategori
        ]);
    }

    /**
     * Proses update data pemilih
     */
    public function update($id)
    {
        $userModel = new \App\Models\UserModel();
        $kategoriModel = new \App\Models\KategoriModel();

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->to('/admin/pemilih')->with('error', 'User tidak ditemukan.');
        }

        $kategori_id = $this->request->getPost('kategori_id');

        // Validasi kategori milik admin
        if ($kategori_id) {
            $cekKategori = $kategoriModel
                ->where('id', $kategori_id)
                ->where('admin_id', session()->get('id'))
                ->first();

            if (!$cekKategori) {
                return redirect()->back()->with('error', 'Kategori tidak valid.');
            }
        }

        $oldData = [
            'nama' => $user['nama'],
            'email' => $user['email'],
            'kategori_id' => $user['kategori_id']
        ];

        $newData = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email') ?: null,
            'kategori_id' => $kategori_id ?: null
        ];

        $userModel->update($id, $newData);

        // Log aktivitas
        $logModel = new \App\Models\AdminActivityLogModel();
        $logModel->logActivity(
            session()->get('id'),
            'update',
            'pemilih',
            $id,
            'Mengedit data pemilih: ' . $user['username'],
            $oldData,
            $newData
        );

        return redirect()->to('/admin/pemilih')->with('success', 'Data pemilih berhasil diupdate.');
    }

    /**
     * Tampilkan form reset password dengan alasan
     */
    public function resetForm($id)
    {
        $userModel = new \App\Models\UserModel();

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->to('/admin/pemilih')->with('error', 'User tidak ditemukan.');
        }

        return view('admin/pemilih/reset', [
            'user' => $user
        ]);
    }

    /**
     * Proses reset password dengan alasan wajib
     */
    public function reset($id)
    {
        $userModel = new \App\Models\UserModel();

        // Ambil alasan dari select atau textarea
        $alasan_pilihan = $this->request->getPost('alasan_pilihan');
        $alasan_lain = $this->request->getPost('alasan');

        // Gabungkan alasan
        if ($alasan_pilihan === 'lainnya' && !empty($alasan_lain)) {
            $alasan = $alasan_lain;
        } else {
            $alasan = $alasan_pilihan;
        }

        // Validasi alasan wajib diisi
        if (empty($alasan) || strlen(trim($alasan)) < 5) {
            return redirect()->back()->with('error', 'Alasan reset password wajib diisi minimal 5 karakter.');
        }

        $user = $userModel
            ->where('id', $id)
            ->where('admin_id', session()->get('id'))
            ->first();

        if (!$user) {
            return redirect()->to('/admin/pemilih')->with('error', 'User tidak ditemukan.');
        }

        // Generate password baru
        $newPassword = 'pass' . rand(1000, 9999);

        $oldData = [
            'password' => '*** (tersembunyi)'
        ];

        $newData = [
            'password' => '*** (di-reset)'
        ];

        $userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        // Log aktivitas dengan alasan
        $logModel = new \App\Models\AdminActivityLogModel();
        $logModel->logActivity(
            session()->get('id'),
            'reset_password',
            'pemilih',
            $id,
            'Mereset password pengguna: ' . $user['username'] . ' | Alasan: ' . $alasan,
            $oldData,
            $newData
        );

        // Tampilkan popup dengan password baru
        return view('admin/pemilih/reset_success', [
            'user' => $user,
            'new_password' => $newPassword
        ]);
    }
}
