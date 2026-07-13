-- =====================================================================
-- SCHEMA DATABASE "COATING CEPAT"
-- Kompatibel: MySQL/MariaDB (phpMyAdmin InfinityFree & Hostinger)
-- Engine: InnoDB | Charset: utf8mb4
-- Cara pakai: import file ini DULU, lalu import seed.sql
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- TABEL: admins  (FITUR: Login Admin)
-- ============================================================
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL, -- hasil password_hash()
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: login_attempts  (FITUR: Proteksi brute force login admin)
-- ============================================================
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address`   VARCHAR(45) NOT NULL,
  `attempts`     INT UNSIGNED NOT NULL DEFAULT 0,
  `last_attempt` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_login_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: services  (FITUR: Layanan / Kelola Layanan)
-- ============================================================
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(170) NOT NULL,
  `price`       DECIMAL(12,0) NOT NULL DEFAULT 0,      -- harga mulai (Rupiah)
  `description` TEXT NOT NULL,                          -- deskripsi lengkap
  `benefits`    TEXT NULL,                              -- benefit, dipisah baris baru
  `duration`    VARCHAR(100) NULL,                      -- estimasi pengerjaan
  `category`    VARCHAR(50) NOT NULL DEFAULT 'coating', -- relasi ke kategori galeri
  `thumbnail`   VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_services_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: bookings  (FITUR: Booking Online + Tracking + Kelola Booking)
-- ============================================================
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_code` VARCHAR(30) NOT NULL,                  -- mis. CC-20260607-X4F9
  `name`         VARCHAR(150) NOT NULL,
  `phone`        VARCHAR(20)  NOT NULL,                 -- nomor WhatsApp
  `email`        VARCHAR(150) NOT NULL,
  `car_brand`    VARCHAR(80)  NOT NULL,
  `car_type`     VARCHAR(80)  NOT NULL,
  `car_year`     SMALLINT UNSIGNED NOT NULL,
  `car_color`    VARCHAR(50)  NOT NULL,
  `service_id`   INT UNSIGNED NOT NULL,
  `booking_date` DATE NOT NULL,
  `booking_time` VARCHAR(10) NOT NULL,                  -- slot jam, mis. 09:00
  `notes`        TEXT NULL,
  `status`       ENUM('pending','confirmed','rescheduled','done','rejected') NOT NULL DEFAULT 'pending',
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bookings_code` (`booking_code`),
  KEY `idx_bookings_date`   (`booking_date`),
  KEY `idx_bookings_status` (`status`),
  CONSTRAINT `fk_bookings_service` FOREIGN KEY (`service_id`)
    REFERENCES `services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: schedules  (FITUR: Kelola Jadwal - tanggal tutup/libur)
-- Kuota harian & slot jam disimpan di tabel settings (key-value)
-- ============================================================
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `closed_date` DATE NOT NULL,
  `reason`      VARCHAR(150) NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_schedules_date` (`closed_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: gallery  (FITUR: Galeri / Kelola Galeri)
-- ============================================================
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(150) NOT NULL,
  `description` VARCHAR(255) NULL,
  `category`    ENUM('coating','paint-correction','interior','headlamp','engine') NOT NULL DEFAULT 'coating',
  `image`       VARCHAR(255) NOT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: before_after  (FITUR: Before-After slider / Kelola Before-After)
-- ============================================================
DROP TABLE IF EXISTS `before_after`;
CREATE TABLE `before_after` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(150) NOT NULL,
  `service_id`   INT UNSIGNED NULL,
  `before_image` VARCHAR(255) NOT NULL,
  `after_image`  VARCHAR(255) NOT NULL,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ba_service` (`service_id`),
  CONSTRAINT `fk_ba_service` FOREIGN KEY (`service_id`)
    REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: testimonials  (FITUR: Testimoni / Kelola Testimoni)
-- ============================================================
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `vehicle`    VARCHAR(120) NOT NULL,                    -- kendaraan pelanggan
  `rating`     TINYINT UNSIGNED NOT NULL DEFAULT 5,      -- 1 s/d 5
  `comment`    TEXT NOT NULL,
  `photo`      VARCHAR(255) NULL,                        -- foto kendaraan
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: faqs  (FITUR: FAQ / Kelola FAQ)
-- ============================================================
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category`   ENUM('Ceramic Coating','Detailing','Booking') NOT NULL DEFAULT 'Booking',
  `question`   VARCHAR(255) NOT NULL,
  `answer`     TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_faqs_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: settings  (FITUR: Kelola Pengaturan Website, key-value)
-- ============================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` TEXT NULL,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
