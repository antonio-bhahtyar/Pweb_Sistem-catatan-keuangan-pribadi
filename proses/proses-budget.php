<?php
// proses/proses-budget.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ====================== TAMBAH BUDGET ======================
if (isset($_POST['tambah_budget'])) {
    $kategori_id = $_POST['kategori_id'];
    $jumlah      = $_POST['jumlah'];
    $bulan       = $_POST['bulan'];
    $tahun       = $_POST['tahun'];

    try {
        $check = $pdo->prepare("SELECT id FROM kategori WHERE id = ? AND user_id = ?");
        $check->execute([$kategori_id, $user_id]);
        if (!$check->fetch()) {
            $_SESSION['pesan'] = "Kategori tidak valid!";
            $_SESSION['tipe_pesan'] = "danger";
            header("Location: " . $base_url . "/pages/budget/index.php?bulan=$bulan&tahun=$tahun");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO anggaran (user_id, kategori_id, bulan, tahun, jumlah) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $kategori_id, $bulan, $tahun, $jumlah]);
        $_SESSION['pesan'] = "Anggaran berhasil ditambahkan!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['pesan'] = "Anggaran untuk kategori ini sudah ada di bulan tersebut!";
        } else {
            $_SESSION['pesan'] = "Gagal menambahkan anggaran!";
        }
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/budget/index.php?bulan=$bulan&tahun=$tahun");
    exit;
}

// ====================== UPDATE BUDGET ======================
if (isset($_POST['update_budget'])) {
    $id     = $_POST['id'];
    $jumlah = $_POST['jumlah'];
    $bulan  = $_POST['bulan'];
    $tahun  = $_POST['tahun'];

    try {
        $stmt = $pdo->prepare("UPDATE anggaran SET jumlah = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$jumlah, $id, $user_id]);
        $_SESSION['pesan'] = "Anggaran berhasil diupdate!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengupdate anggaran!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/budget/index.php?bulan=$bulan&tahun=$tahun");
    exit;
}

// ====================== HAPUS BUDGET ======================
if (isset($_POST['hapus_budget'])) {
    $id    = $_POST['id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    try {
        $stmt = $pdo->prepare("DELETE FROM anggaran WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $_SESSION['pesan'] = "Anggaran berhasil dihapus!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal menghapus anggaran!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/budget/index.php?bulan=$bulan&tahun=$tahun");
    exit;
}

header("Location: " . $base_url . "/pages/budget/index.php");
exit;
