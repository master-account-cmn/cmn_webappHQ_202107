-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2021-07-07 10:12:16
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
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `remember_token`, `display_name`, `authority`, `email`, `email_verified_at`, `validity`, `delete_flag`, `created_at`, `updated_at`) VALUES
(1, 'aaaaaaa', 'adminadmin', NULL, '小松宏樹', 10, 'test@test', NULL, 0, 0, '2021-06-14 06:11:40', '2021-06-18 08:37:54'),
(2, 'bbbbbbbb', '12345678', NULL, 'test_ac', 10, 'admin@admin', NULL, 0, 0, '2021-06-14 06:13:15', '2021-06-22 07:22:29'),
(5, 'sample01', 'password', NULL, 'sample００１', 1, 'sample@sample', NULL, 0, 0, '2021-06-15 01:30:22', '2021-06-18 03:01:03');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
