-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 17, 2026 at 10:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lab_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', 'admin123', 'Administrator', '2026-11-11 04:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id` int(11) NOT NULL,
  `kode_alat` varchar(20) NOT NULL,
  `nama_alat` varchar(150) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `jumlah_total` int(11) NOT NULL DEFAULT 0,
  `jumlah_tersedia` int(11) NOT NULL DEFAULT 0,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT 'Baik',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id`, `kode_alat`, `nama_alat`, `kategori_id`, `jumlah_total`, `jumlah_tersedia`, `kondisi`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'ALT-001', 'Multimeter Digital', 1, 10, 0, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-17 06:45:04'),
(2, 'ALT-002', 'Osiloskop', 1, 5, 4, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-14 06:55:34'),
(3, 'ALT-003', 'Power Supply', 2, 8, 8, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-11 04:10:34'),
(4, 'ALT-004', 'Solder', 2, 15, 15, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-14 06:31:11'),
(5, 'ALT-005', 'Breadboard', 2, 20, 20, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-11 04:10:34'),
(6, 'ALT-006', 'Arduino Uno', 2, 12, 12, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-11 04:10:34'),
(7, 'ALT-007', 'Raspberry Pi', 4, 6, 6, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-11 04:10:34'),
(8, 'ALT-008', 'Kacamata Pelindung', 5, 25, 25, 'Baik', NULL, '2026-11-11 04:10:34', '2026-11-11 04:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `alat_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id`, `peminjaman_id`, `alat_id`, `jumlah`) VALUES
(7, 7, 1, 4),
(8, 8, 2, 1),
(9, 9, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `created_at`) VALUES
(1, 'Alat Ukur', '2026-11-11 04:10:34'),
(2, 'Alat Elektronik', '2026-11-11 04:10:34'),
(3, 'Alat Mekanik', '2026-11-11 04:10:34'),
(4, 'Komputer & Peripheral', '2026-11-11 04:10:34'),
(5, 'Alat Keselamatan', '2026-11-11 04:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `peminjam`
--

CREATE TABLE `peminjam` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `kelas` varchar(30) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjam`
--

INSERT INTO `peminjam` (`id`, `nama`, `nim`, `kelas`, `no_hp`, `created_at`, `password`) VALUES
(1, 'dilla', '123', 'if-a', '0987', '2026-11-13 12:14:53', ''),
(2, 'gina', '032', 'if-a', '09827', '2026-11-13 12:19:38', ''),
(3, 'anisya', '123250009', 'IF-A', '08226721927', '2026-11-13 15:14:34', ''),
(4, 'cinta', '123250002', 'IF-B', '082292277980', '2026-11-14 06:51:41', ''),
(5, 'reni', '123250013', 'if-a', '08291783861', '2026-11-17 06:45:04', ''),
(6, 'anisya', '123250024', 'if-a', '0987654', '2026-11-17 08:12:39', '$2y$10$EQZs9FQFH/JOMAfZ9KIaQ.MeyYy2Ov7cjY5SDLX.y//npSyhMlaVC'),
(7, 'marina', '123250212', 'if-i', '123456', '2026-11-17 08:14:50', '$2y$10$q7p9FIDFcaXnULWxOzmj/OOtfcyqbx0AczxPh7Hv7HaUyLbWdlk8.');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `kode_pinjam` varchar(20) NOT NULL,
  `peminjam_id` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan','Terlambat') DEFAULT 'Dipinjam',
  `kondisi_kembali` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `kode_pinjam`, `peminjam_id`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `tanggal_kembali_aktual`, `status`, `kondisi_kembali`, `catatan`, `created_at`, `updated_at`) VALUES
(7, 'PJM-2026-007', 4, '2026-11-15', '2026-11-16', '2026-11-16', 'Dikembalikan', 'Baik', 'memuaskan', '2026-11-14 06:52:02', '2026-11-14 06:52:49'),
(8, 'PJM-2026-008', 3, '2026-11-14', '2026-11-15', '2026-11-15', 'Dikembalikan', 'Rusak Ringan', 'memuaskan', '2026-11-14 06:55:34', '2026-11-14 06:56:03'),
(9, 'PJM261117074504', 5, '2026-11-17', '2027-11-12', NULL, 'Dipinjam', NULL, NULL, '2026-11-17 06:45:04', '2026-11-17 06:45:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_alat` (`kode_alat`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `alat_id` (`alat_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjam`
--
ALTER TABLE `peminjam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pinjam` (`kode_pinjam`),
  ADD KEY `peminjam_id` (`peminjam_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peminjam`
--
ALTER TABLE `peminjam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `alat_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`peminjam_id`) REFERENCES `peminjam` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
