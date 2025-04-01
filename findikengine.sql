-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 01 Nis 2025, 00:32:40
-- Sunucu sürümü: 8.0.30
-- PHP Sürümü: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `findikengine`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `username`, `email`, `password`, `profile_image`, `bio`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kullanıcı 1', 'Soyad 1', 'kullanici1', 'kullanici1@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:29:18'),
(2, 'Kullanıcı 2', 'Soyad 2', 'kullanici2', 'kullanici2@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(3, 'Kullanıcı 3', 'Soyad 3', 'kullanici3', 'kullanici3@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(4, 'Kullanıcı 4', 'Soyad 4', 'kullanici4', 'kullanici4@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(5, 'Kullanıcı 5', 'Soyad 5', 'kullanici5', 'kullanici5@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(6, 'Kullanıcı 6', 'Soyad 6', 'kullanici6', 'kullanici6@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(7, 'Kullanıcı 7', 'Soyad 7', 'kullanici7', 'kullanici7@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(8, 'Kullanıcı 8', 'Soyad 8', 'kullanici8', 'kullanici8@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(9, 'Kullanıcı 9', 'Soyad 9', 'kullanici9', 'kullanici9@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10'),
(10, 'Kullanıcı 10', 'Soyad 10', 'kullanici10', 'kullanici10@mail.com', '$2y$10$YF4d7Lxp9AmWYlTo.62i/ukYfhGFztfrc9QUM3.IUJgHksZvcN4GO', 'default.png', 'Bu bir demo kullanıcıdır.', 'active', '2025-03-31 23:18:45', '2025-03-31 23:31:10');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
