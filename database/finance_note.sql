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