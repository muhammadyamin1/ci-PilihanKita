-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Okt 2025 pada 05.37
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pilihan_kita`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `calon`
--

CREATE TABLE `calon` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `nama_calon` varchar(100) DEFAULT NULL,
  `wakil_calon` varchar(100) DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `kategori` enum('ketua_kelas','osis','kating','bem') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_pemilihan`
--

CREATE TABLE `kategori_pemilihan` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 0,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `suara`
--

CREATE TABLE `suara` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `calon_id` int(11) DEFAULT NULL,
  `waktu_pilih` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `identifier` varchar(50) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `generated` tinyint(1) DEFAULT 0,
  `sudah_memilih` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `admin_id`, `username`, `password`, `nama`, `identifier`, `role`, `generated`, `sudah_memilih`, `created_at`) VALUES
(1, NULL, 'yamin123', '$2y$10$a6f5B5ew9PIljHVpPgv2meNXKXETmD5fcObzNgXuB1wexJHXE5j7S', 'Administrator', NULL, 'admin', 0, 0, '2025-10-16 10:28:48'),
(2, NULL, 'user', '$2y$10$VGlqMFgkDUu0j5Mq5TggZOAnT55sJ4ikqJOH.YmoXXWwG8PYJQTy6', 'User', NULL, 'user', 0, 0, '2025-10-16 10:29:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_import_log`
--

CREATE TABLE `user_import_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `jumlah_user` int(11) DEFAULT NULL,
  `jenis` enum('upload','generate') DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `calon`
--
ALTER TABLE `calon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_calon_admin` (`admin_id`);

--
-- Indeks untuk tabel `kategori_pemilihan`
--
ALTER TABLE `kategori_pemilihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kategori_admin` (`admin_id`);

--
-- Indeks untuk tabel `suara`
--
ALTER TABLE `suara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_suara_user` (`user_id`),
  ADD KEY `fk_suara_calon` (`calon_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_admin` (`admin_id`);

--
-- Indeks untuk tabel `user_import_log`
--
ALTER TABLE `user_import_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_admin` (`admin_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `calon`
--
ALTER TABLE `calon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori_pemilihan`
--
ALTER TABLE `kategori_pemilihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `suara`
--
ALTER TABLE `suara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user_import_log`
--
ALTER TABLE `user_import_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `calon`
--
ALTER TABLE `calon`
  ADD CONSTRAINT `fk_calon_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kategori_pemilihan`
--
ALTER TABLE `kategori_pemilihan`
  ADD CONSTRAINT `fk_kategori_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `suara`
--
ALTER TABLE `suara`
  ADD CONSTRAINT `fk_suara_calon` FOREIGN KEY (`calon_id`) REFERENCES `calon` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_suara_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_import_log`
--
ALTER TABLE `user_import_log`
  ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
