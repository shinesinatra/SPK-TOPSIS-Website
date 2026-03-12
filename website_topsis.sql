-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Mar 2026 pada 02.40
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
-- Database: `website_topsis`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE `alternatif` (
  `nisn` varchar(20) NOT NULL,
  `kelas` varchar(5) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` varchar(15) NOT NULL,
  `alamat` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`nisn`, `kelas`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`) VALUES
('123', 'VI', 'Alternatif 1', '2013-01-01', 'Perempuan', 'Alamat 1'),
('124', 'VI', 'Alternatif 2', '2013-01-02', 'Laki-laki', 'Alamat 2'),
('125', 'VI', 'Alternatif 3', '2013-01-03', 'Laki-laki', 'Alamat 3'),
('126', 'VI', 'Alternatif 4', '2013-01-04', 'Laki-laki', 'Alamat 4'),
('127', 'VI', 'Alternatif 5', '2013-01-05', 'Perempuan', 'Alamat 5');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kriteria`
--

CREATE TABLE `kriteria` (
  `kode` varchar(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `bobot` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kriteria`
--

INSERT INTO `kriteria` (`kode`, `nama`, `bobot`) VALUES
('C1', 'Nilai Rata-Rata Rapor', 0.3),
('C2', 'Presentase Kehadiran', 0.2),
('C3', 'Keaktifan Ekstrakulikuler', 0.15),
('C4', 'Keaktifan Kompetisi', 0.15),
('C5', 'Penilaian Sikap', 0.2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `nik` varchar(10) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`nik`, `full_name`, `username`, `password`) VALUES
('nik001', 'Administrator', 'admin', 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian`
--

CREATE TABLE `penilaian` (
  `kelas` varchar(10) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nilai_rata_rata_rapor` double DEFAULT NULL,
  `presentase_kehadiran` double DEFAULT NULL,
  `keaktifan_ekstrakulikuler` double DEFAULT NULL,
  `keaktifan_kompetisi` double DEFAULT NULL,
  `penilaian_sikap` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penilaian`
--

INSERT INTO `penilaian` (`kelas`, `nisn`, `nama`, `nilai_rata_rata_rapor`, `presentase_kehadiran`, `keaktifan_ekstrakulikuler`, `keaktifan_kompetisi`, `penilaian_sikap`) VALUES
('VI', '123', 'Alternatif 1', 8.5, 8.9, 6.5, 6, 9),
('VI', '124', 'Alternatif 2', 8.3, 9.1, 7, 6, 9.2),
('VI', '125', 'Alternatif 3', 7.9, 9.6, 7, 7, 9.1),
('VI', '126', 'Alternatif 4', 8.1, 9.5, 6, 6, 9.3),
('VI', '127', 'Alternatif 5', 8.3, 9.4, 6.5, 6.5, 9.5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `perankingan`
--

CREATE TABLE `perankingan` (
  `nisn` varchar(50) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nilai` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `perankingan`
--

INSERT INTO `perankingan` (`nisn`, `nama`, `nilai`) VALUES
('123', 'Alternatif 1', 0.4335983168639),
('124', 'Alternatif 2', 0.49828552266082),
('125', 'Alternatif 3', 0.60590180122474),
('126', 'Alternatif 4', 0.30851310264108),
('127', 'Alternatif 5', 0.5914594537765);

-- --------------------------------------------------------

--
-- Struktur dari tabel `periode`
--

CREATE TABLE `periode` (
  `ajaran` varchar(20) NOT NULL,
  `tahun` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`nisn`);

--
-- Indeks untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`kode`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`nik`);

--
-- Indeks untuk tabel `perankingan`
--
ALTER TABLE `perankingan`
  ADD PRIMARY KEY (`nisn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
