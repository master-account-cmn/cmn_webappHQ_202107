-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2021-07-07 11:02:40
-- サーバのバージョン： 10.4.18-MariaDB
-- PHP のバージョン: 8.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `cms`
--

--
-- テーブルのデータのダンプ `contents`
--

INSERT INTO `contents` (`id`, `cid`, `name`, `display_name`, `content_type`, `file_name`, `file_type`, `file_size`, `display_time`, `delete_flag`, `created_at`, `updated_at`) VALUES
(1, 'v1', '001', 'tictok01', 10, 'tictok01.mp4', 'video/mp4', '22958073', '131.375', 0, '2021-06-21 06:55:45', '2021-06-21 06:55:45'),
(2, 'v2', '002', 'tictok02', 10, 'tictok02.mp4', 'video/mp4', '787899', '14.673', 0, '2021-06-21 06:56:01', '2021-06-21 06:56:01'),
(3, 'v3', '003', 'tictok03', 10, 'tictok03.mp4', 'video/mp4', '6534219', '125.388', 0, '2021-06-21 06:56:20', '2021-06-21 06:56:20'),
(4, 'v4', '004', 'tictok04', 10, 'tictok04.mp4', 'video/mp4', '2869916', '15.903', 0, '2021-06-21 06:56:41', '2021-06-21 06:56:41'),
(5, 'v5', 'tictok06', 'tictok06', 10, 'tictok06.mp4', 'video/mp4', '5933331', '57.606', 0, '2021-06-22 01:30:03', '2021-06-22 01:30:03'),
(6, 'v6', '20210622', '123456', 10, 'mov_hts-samp001.mp4', 'video/mp4', '3523218', '13.5135', 0, '2021-06-22 09:12:09', '2021-06-22 09:12:09');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
