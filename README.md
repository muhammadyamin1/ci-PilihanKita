# Pilihan Kita

Aplikasi voting sederhana berbasis CodeIgniter 4 untuk pemilihan ketua kelas, OSIS, atau BEM.

Proyek ini dibuat untuk kebutuhan voting/election yang mendukung:
- Manajemen admin dan pemilih
- Pengaturan kategori pemilihan
- CRUD calon dengan unggah foto
- Import/generate pemilih otomatis
- Proses voting oleh pemilih terdaftar
- Kontrol akses berbasis peran (admin/user)

---

## Fitur Utama

- halaman login dan logout
- Admin dapat:
  - membuat/hapus kategori pemilihan
  - menambahkan, mengedit, dan menghapus calon
  - mengelola pemilih, impor CSV, dan generate akun pemilih
  - mengubah profil, email, dan foto profil
  - membersihkan data atau menu maintenance khusus
- User (pemilih) dapat:
  - login dengan akun terdaftar
  - melihat daftar calon sesuai kategori
  - memilih satu calon
  - mengubah password default saat pertama login
- Upload foto calon disimpan di `writable/uploads/calon/`
- Semua akses admin/user dilindungi menggunakan filter CodeIgniter

---

## Teknologi

- PHP 8.1+
- CodeIgniter 4
- MySQL
- Composer
- `public/` sebagai folder root web

---

## Struktur Utama

- `app/Controllers/` - controller aplikasi
- `app/Controllers/Admin/` - controller admin
- `app/Models/` - model untuk `users`, `calon`, `kategori`, `pemilih`, `suara`
- `app/Config/Routes.php` - routing utama aplikasi
- `app/Views/` - tampilan frontend/backoffice
- `public/` - titik masuk aplikasi
- `writable/uploads/` - file upload dan export CSV

---

## Instalasi

1. Clone repository:
   ```bash
   git clone https://github.com/<username>/ci-PilihanKita.git
   cd ci-PilihanKita
   ```
2. Install depedensi Composer:
   ```bash
   composer install
   ```
3. Konfigurasi database di `app/Config/Database.php` atau file `.env`:
   - database: `pilihan_kita`
   - user: `root` (sesuaikan dengan setup lokal Anda)
4. Impor struktur database:
   - gunakan file `pilihan_kita.sql`
5. Jalankan server lokal:
   ```bash
   php spark serve
   ```
6. Akses aplikasi melalui browser:
   ```text
   http://localhost:8080
   ```

> Pastikan web server Anda diarahkan ke folder `public/` agar struktur CodeIgniter berjalan dengan aman.

---

## Routing Penting

- `/` → Landing page
- `/login` → Form login
- `/admin/dashboard` → Dashboard admin
- `/admin/kategori` → Kelola kategori
- `/admin/calon` → Kelola calon
- `/admin/pemilih` → Kelola pemilih
- `/user/pemilihan` → Halaman voting pemilih
- `/auth/logout` → Logout

---

## Setup Awal dan Penggunaan

1. Siapkan akun admin manual di tabel `users` jika belum ada.
2. Login sebagai admin.
3. Tambahkan kategori pemilihan.
4. Tambahkan calon di kategori tersebut.
5. Buat atau import pemilih, lalu arahkan pemilih untuk login.
6. Pemilih dapat memilih calon dan melihat konfirmasi suara.

---

## Catatan Pengembangan

- Password user disimpan dengan `password_hash()` dan diperiksa dengan `password_verify()`.
- User yang di-generate otomatis diberi flag `generated = 1` dan wajib ubah password sebelum memilih.
- Sistem voting hanya menerima 1 suara per user.
- Foto calon dilayani melalui `UploadController::showCalon()`.

---

## File Tambahan

- `pilihan_kita.sql` - dump database awal
- `phpunit.xml.dist` - konfigurasi testing
- `spark` - CLI CodeIgniter

---

## Lisensi

Proyek ini menggunakan lisensi MIT.
