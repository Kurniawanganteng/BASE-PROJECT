-- ============================================
-- Product Manager - Database Schema
-- Import file ini di phpMyAdmin
-- ============================================

CREATE DATABASE IF NOT EXISTS product_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE product_manager;

-- Tabel produk
CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(255) NOT NULL UNIQUE,
    kategori    VARCHAR(100) NOT NULL,
    harga       DECIMAL(15,2) NOT NULL,
    stok        INT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Data awal (seeder)
INSERT INTO products (nama, kategori, harga, stok) VALUES
('Laptop ASUS ROG',         'Elektronik', 15000000, 5),
('Mouse Wireless Logitech', 'Aksesoris',  250000,   2),
('Keyboard Mechanical',     'Aksesoris',  750000,   10),
('Monitor LED 24 inch',     'Elektronik', 2200000,  1),
('SSD NVMe 1TB',            'Komponen',   1350000,  8);
