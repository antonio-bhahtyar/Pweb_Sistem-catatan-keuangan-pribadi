CREATE DATABASE IF NOT EXISTS finance_note 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE finance_note;

-- TABEL USERS
CREATE TABLE users (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap        VARCHAR(100) NOT NULL,
    email               VARCHAR(100) NOT NULL UNIQUE,
    password            VARCHAR(255) NOT NULL,
    role                VARCHAR(20) DEFAULT 'user',
    mata_uang_default   VARCHAR(10) DEFAULT 'IDR',
    foto_profil         VARCHAR(255) NULL,
    tanggal_daftar      DATETIME DEFAULT CURRENT_TIMESTAMP,
    terakhir_login      DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL KATEGORI
CREATE TABLE kategori (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    nama_kategori   VARCHAR(100) NOT NULL,
    tipe            ENUM('pemasukan', 'pengeluaran') NOT NULL,
    warna           VARCHAR(7) DEFAULT '#6c757d',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL TRANSAKSI
CREATE TABLE transaksi (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    kategori_id     INT NOT NULL,
    tipe            ENUM('pemasukan', 'pengeluaran') NOT NULL,
    jumlah          DECIMAL(15,2) NOT NULL,
    mata_uang       VARCHAR(10) NOT NULL DEFAULT 'IDR',
    tanggal         DATE NOT NULL,
    keterangan      TEXT NULL,
    bukti           VARCHAR(255) NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL ANGGARAN
CREATE TABLE anggaran (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    kategori_id     INT NOT NULL,
    bulan           INT NOT NULL,
    tahun           INT NOT NULL,
    jumlah          DECIMAL(15,2) NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_anggaran (user_id, kategori_id, bulan, tahun),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================================
-- SEEDERS (Contoh Data Awal)
-- Password default semua user: "password123"
-- Hash dihasilkan dari password_hash('password123', PASSWORD_DEFAULT)
-- ======================================================

-- CATATAN: Jalankan database/seed.php SETELAH file SQL ini diimpor
-- agar password ter-hash dengan benar (password_hash di runtime).
-- Default password untuk admin & user demo: "password123"
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Administrator', 'admin@financenote.test', '__PLACEHOLDER__', 'admin'),
('Budi User',     'budi@financenote.test',  '__PLACEHOLDER__', 'user');

INSERT INTO kategori (user_id, nama_kategori, tipe, warna) VALUES
(2, 'Gaji',       'pemasukan',   '#198754'),
(2, 'Bonus',      'pemasukan',   '#20c997'),
(2, 'Makan',      'pengeluaran', '#dc3545'),
(2, 'Transport',  'pengeluaran', '#fd7e14'),
(2, 'Tagihan',    'pengeluaran', '#6f42c1');

INSERT INTO transaksi (user_id, kategori_id, tipe, jumlah, tanggal, keterangan) VALUES
(2, 1, 'pemasukan',   5000000, CURDATE() - INTERVAL 10 DAY, 'Gaji bulan ini'),
(2, 3, 'pengeluaran',  150000, CURDATE() - INTERVAL  5 DAY, 'Makan siang mingguan'),
(2, 4, 'pengeluaran',   75000, CURDATE() - INTERVAL  3 DAY, 'Bensin'),
(2, 5, 'pengeluaran',  300000, CURDATE() - INTERVAL  1 DAY, 'Listrik');

INSERT INTO anggaran (user_id, kategori_id, bulan, tahun, jumlah) VALUES
(2, 3, MONTH(CURDATE()), YEAR(CURDATE()),  800000),
(2, 4, MONTH(CURDATE()), YEAR(CURDATE()),  500000),
(2, 5, MONTH(CURDATE()), YEAR(CURDATE()), 1000000);