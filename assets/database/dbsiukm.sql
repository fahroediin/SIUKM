-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2023 at 01:36 PM
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
-- Database: `dbsiukm`
--

-- --------------------------------------------------------

--
-- Table structure for table `tab_dau`
--

CREATE TABLE `tab_dau` (
  `id_anggota` varchar(15) NOT NULL,
  `id_user` varchar(15) NOT NULL,
  `nama_depan` varchar(25) NOT NULL,
  `nama_belakang` varchar(25) DEFAULT NULL,
  `nim` varchar(15) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `email` varchar(25) NOT NULL,
  `prodi` varchar(20) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `pasfoto` mediumblob NOT NULL,
  `id_ukm` varchar(20) NOT NULL,
  `nama_ukm` varchar(50) NOT NULL,
  `sjk_bergabung` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_galeri`
--

CREATE TABLE `tab_galeri` (
  `id_foto` varchar(15) NOT NULL,
  `id_ukm` varchar(20) NOT NULL,
  `nama_ukm` varchar(50) NOT NULL,
  `id_kegiatan` varchar(15) NOT NULL,
  `nama_kegiatan` varchar(20) NOT NULL,
  `foto_kegiatan` mediumblob NOT NULL,
  `tgl` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_kegiatan`
--

CREATE TABLE `tab_kegiatan` (
  `id_kegiatan` varchar(15) NOT NULL,
  `nama_kegiatan` varchar(50) NOT NULL,
  `id_ukm` varchar(20) NOT NULL,
  `nama_ukm` varchar(50) NOT NULL,
  `tgl_mulai` datetime(6) NOT NULL,
  `tgl_berakhir` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_pacab`
--

CREATE TABLE `tab_pacab` (
  `id_calabar` varchar(15) NOT NULL,
  `id_user` varchar(15) NOT NULL,
  `nama_depan` varchar(25) NOT NULL,
  `nama_belakang` varchar(25) NOT NULL,
  `nim` varchar(15) NOT NULL,
  `email` varchar(25) NOT NULL,
  `no_hp` varchar(13) NOT NULL,
  `prodi` varchar(20) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `id_ukm` varchar(20) NOT NULL,
  `nama_ukm` varchar(50) NOT NULL,
  `pasfoto` mediumblob NOT NULL,
  `foto_ktm` mediumblob NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `nilai_tpa` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_prestasi`
--

CREATE TABLE `tab_prestasi` (
  `id_prestasi` varchar(20) NOT NULL,
  `nama_prestasi` varchar(50) NOT NULL,
  `id_ukm` varchar(20) NOT NULL,
  `nama_ukm` varchar(50) NOT NULL,
  `tgl_prestasi` datetime(6) NOT NULL,
  `penyelenggara` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_strukm`
--

CREATE TABLE `tab_strukm` (
  `id_jabatan` varchar(3) NOT NULL,
  `id_ukm` varchar(20) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `nim` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_ukm`
--

CREATE TABLE `tab_ukm` (
  `id_ukm` varchar(20) NOT NULL,
  `nama_ukm` varchar(50) NOT NULL,
  `logo_ukm` mediumblob NOT NULL,
  `nama_ketua` varchar(50) NOT NULL,
  `nim_ketua` varchar(15) NOT NULL,
  `sejarah` varchar(255) NOT NULL,
  `visi` varchar(255) NOT NULL,
  `misi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tab_user`
--

CREATE TABLE `tab_user` (
  `id_user` varchar(15) NOT NULL,
  `nama_depan` varchar(50) NOT NULL,
  `nama_belakang` varchar(50) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `level` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
