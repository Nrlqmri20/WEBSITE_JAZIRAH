-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jul 2025 pada 03.15
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
-- Database: `database_jazirah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bukti_dukung`
--

CREATE TABLE `bukti_dukung` (
  `id_pendukung` int(11) NOT NULL,
  `id_deskripsi` varchar(11) NOT NULL,
  `kode_satker` int(11) NOT NULL,
  `link` text NOT NULL,
  `keterangan` text NOT NULL,
  `pjk` varchar(100) DEFAULT NULL,
  `target_bulan` varchar(7) DEFAULT NULL,
  `progress` int(3) DEFAULT 0,
  `id_pengguna` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','in_progress','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `deskripsi`
--

CREATE TABLE `deskripsi` (
  `id_deskripsi` varchar(11) NOT NULL,
  `sub_deskripsi` varchar(100) NOT NULL,
  `deskripsi_rencana_kinerja` text NOT NULL,
  `deskripsi_rencana_aksi` text NOT NULL,
  `deskripsi_rencana_output` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kode_satker`
--

CREATE TABLE `kode_satker` (
  `kode_satker` int(11) NOT NULL,
  `satker` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `id_role` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `id_role`, `created_at`, `updated_at`) VALUES
(6, 'andi', '$2y$10$Bd0RdU07iA4HCRF5QHDCauxl5ZHj3bRgQE59us/by50HIoS/K4yQK', 1, '2025-07-22 02:02:19', '2025-07-22 02:31:58'),
(7, 'admin', '$2y$10$IBBh2yifIQM856/Jil0T1.g9d2Re3yKcrNULjbOmlu8bwNhumAzL2', 2, '2025-07-22 02:02:19', '2025-07-23 01:37:14'),
(8, 'siti', '$2y$10$3QalZCiMaG1/9TU9naXHneikyWjfTcHRHk/x4LNnc1Zv6HMweHMU2', 3, '2025-07-22 02:02:19', '2025-07-23 01:42:11'),
(9, 'budi', '$2y$10$XtxTXjA9JxX.baDN0bnf2OEL0UYtH7T0HlYk/WbTHHOH7YdJ83WNe', 4, '2025-07-22 02:02:19', '2025-07-23 01:45:14'),
(10, 'dina', '$2y$10$u/LLi8EFtavvzytbWHddz.rcqCTjx10BmximLCdVArYgJa8WmsfxG', 5, '2025-07-22 02:02:19', '2025-07-23 01:46:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `role_name` enum('admin','operator','kepala_bps','pegawai','pemeriksa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`id_role`, `role_name`) VALUES
(1, 'pegawai'),
(2, 'admin'),
(3, 'operator'),
(4, 'kepala_bps'),
(5, 'pemeriksa');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bukti_dukung`
--
ALTER TABLE `bukti_dukung`
  ADD PRIMARY KEY (`id_pendukung`),
  ADD KEY `id_deskripsi` (`id_deskripsi`),
  ADD KEY `kode_satker` (`kode_satker`),
  ADD KEY `fk_bukti_pegawai` (`id_pengguna`);

--
-- Indeks untuk tabel `deskripsi`
--
ALTER TABLE `deskripsi`
  ADD PRIMARY KEY (`id_deskripsi`);

--
-- Indeks untuk tabel `kode_satker`
--
ALTER TABLE `kode_satker`
  ADD PRIMARY KEY (`kode_satker`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD KEY `id_role` (`id_role`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bukti_dukung`
--
ALTER TABLE `bukti_dukung`
  MODIFY `id_pendukung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kode_satker`
--
ALTER TABLE `kode_satker`
  MODIFY `kode_satker` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bukti_dukung`
--
ALTER TABLE `bukti_dukung`
  ADD CONSTRAINT `bukti_dukung_ibfk_1` FOREIGN KEY (`id_deskripsi`) REFERENCES `deskripsi` (`id_deskripsi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bukti_dukung_ibfk_2` FOREIGN KEY (`kode_satker`) REFERENCES `kode_satker` (`kode_satker`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bukti_pegawai` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
