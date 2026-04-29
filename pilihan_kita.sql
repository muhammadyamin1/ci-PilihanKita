-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Apr 2026 pada 05.29
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
-- Struktur dari tabel `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `kategori_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `calon`
--

INSERT INTO `calon` (`id`, `admin_id`, `nama_calon`, `wakil_calon`, `visi`, `misi`, `foto`, `kategori_id`) VALUES
(76, 1, 'Ari', 'Budi', 'Kampus Emas 2044', '1. Meningkatkan kompetensi mahasiswa/i.\r\n2. Membuat lingkungan kampus adil dan sejahtera.', 'uploads/calon/ari_budi.jpg', 4);

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

--
-- Dumping data untuk tabel `kategori_pemilihan`
--

INSERT INTO `kategori_pemilihan` (`id`, `nama`, `aktif`, `admin_id`) VALUES
(1, 'Ketua Kelas', 0, 1),
(2, 'OSIS', 0, 1),
(3, 'Kating', 0, 1),
(4, 'BEM', 1, 1);

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
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `kategori_id` int(11) DEFAULT NULL,
  `generated` tinyint(1) DEFAULT 0,
  `sudah_memilih` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `admin_id`, `username`, `password`, `nama`, `email`, `role`, `kategori_id`, `generated`, `sudah_memilih`, `created_at`, `foto`, `verification_code`, `verification_expires`) VALUES
(1, NULL, 'yamin123', '$2y$10$/R22qg4nWR6oE9jOCkPWg.cBNwtr2ZOsL4Z0MUw50xxl/YhujxZDO', 'Administrator', 'muhammadyamin1081@gmail.com', 'admin', NULL, 0, 0, '2025-10-16 10:28:48', 'user_1_1777347696.jpg', '312766', '2026-04-28 04:57:17');

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
-- Indeks untuk tabel `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `calon`
--
ALTER TABLE `calon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_calon_admin` (`admin_id`),
  ADD KEY `fk_calon_kategori` (`kategori_id`);

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
  ADD KEY `fk_users_admin` (`admin_id`),
  ADD KEY `fk_users_kategori` (`kategori_id`);

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
-- AUTO_INCREMENT untuk tabel `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `calon`
--
ALTER TABLE `calon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `kategori_pemilihan`
--
ALTER TABLE `kategori_pemilihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `suara`
--
ALTER TABLE `suara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `fk_calon_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_calon_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_pemilihan` (`id`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_users_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_pemilihan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_import_log`
--
ALTER TABLE `user_import_log`
  ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
