-- Database: db_perpustakaan
-- Import lewat phpMyAdmin atau: mysql -u root < db_perpustakaan.sql

CREATE DATABASE IF NOT EXISTS db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_perpustakaan;

DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE buku (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  penulis VARCHAR(100) NOT NULL,
  tahun_terbit YEAR NOT NULL,
  stok INT UNSIGNED NOT NULL DEFAULT 0,
  sampul VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Akun demo: admin / admin123 (hash bcrypt via password_hash)
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$3wK2PfV59udeV8RQ7xuzi.kND7yDoEJEbE1tgq6lsT7jrol4mbm1W', 'Administrator');

INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES
('Pemrograman Web dengan PHP', 'Teknologi', 'Budi Raharjo', 2021, 8, 'sampul_pemrograman_php.jpg'),
('Basis Data MySQL', 'Teknologi', 'Siti Aminah', 2020, 3, 'sampul_basis_data.jpg'),
('Analisis Sistem Informasi', 'Pendidikan', 'Andi Wijaya', 2019, 0, 'sampul_sistem_informasi.jpg'),
('Algoritma & Struktur Data', 'Sains', 'Rina Kusuma', 2022, 12, 'sampul_algoritma.jpg'),
('Bisnis Digital', 'Bisnis', 'Dewi Lestari', 2023, 1, 'sampul_bisnis_digital.jpg'),
('Sejarah Komputer', 'Sejarah', 'Hendra Saputra', 2018, 0, 'sampul_sejarah_komputer.jpg');
