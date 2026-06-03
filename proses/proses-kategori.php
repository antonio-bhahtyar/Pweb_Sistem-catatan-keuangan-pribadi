<?php
// proses/proses-kategori.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ====================== TAMBAH KATEGORI ======================
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = trim($_POST['nama_kategori']);
    $tipe          = $_POST['tipe'];
    $warna         = $_POST['warna'] ?? '#6c757d';

    if (empty($nama_kategori)) {
        $_SESSION['pesan'] = "Nama kategori harus diisi!";
        $_SESSION['tipe_pesan'] = "danger";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO kategori (user_id, nama_kategori, tipe, warna) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $nama_kategori, $tipe, $warna]);
            $_SESSION['pesan'] = "Kategori berhasil ditambahkan!";
            $_SESSION['tipe_pesan'] = "success";
        } catch (PDOException $e) {
            $_SESSION['pesan'] = "Gagal menambahkan kategori!";
            $_SESSION['tipe_pesan'] = "danger";
        }
    }

    header("Location: " . $base_url . "/pages/kategori/index.php");
    exit;
}

// ====================== UPDATE KATEGORI ======================
if (isset($_POST['update_kategori'])) {
    $id            = $_POST['id'];
    $nama_kategori = trim($_POST['nama_kategori']);
    $tipe          = $_POST['tipe'];
    $warna         = $_POST['warna'] ?? '#6c757d';

    if (empty($nama_kategori)) {
        $_SESSION['pesan'] = "Nama kategori harus diisi!";
        $_SESSION['tipe_pesan'] = "danger";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, tipe = ?, warna = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$nama_kategori, $tipe, $warna, $id, $user_id]);
            $_SESSION['pesan'] = "Kategori berhasil diupdate!";
            $_SESSION['tipe_pesan'] = "success";
        } catch (PDOException $e) {
            $_SESSION['pesan'] = "Gagal mengupdate kategori!";
            $_SESSION['tipe_pesan'] = "danger";
        }
    }

    header("Location: " . $base_url . "/pages/kategori/index.php");
    exit;
}

// ====================== HAPUS KATEGORI ======================
if (isset($_POST['hapus_kategori'])) {
    $id = $_POST['id'];

    try {
        $check = $pdo->prepare("SELECT COUNT(*) as total FROM transaksi WHERE kategori_id = ? AND user_id = ?");
        $check->execute([$id, $user_id]);
        $result = $check->fetch();

        if ($result['total'] > 0) {
            $_SESSION['pesan'] = "Kategori tidak dapat dihapus karena masih digunakan oleh transaksi!";
            $_SESSION['tipe_pesan'] = "warning";
        } else {
            $check = $pdo->prepare("SELECT COUNT(*) as total FROM anggaran WHERE kategori_id = ? AND user_id = ?");
            $check->execute([$id, $user_id]);
            $result = $check->fetch();

            if ($result['total'] > 0) {
                $_SESSION['pesan'] = "Kategori tidak dapat dihapus karena masih digunakan oleh anggaran!";
                $_SESSION['tipe_pesan'] = "warning";
            } else {
                $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = ? AND user_id = ?");
                $stmt->execute([$id, $user_id]);
                $_SESSION['pesan'] = "Kategori berhasil dihapus!";
                $_SESSION['tipe_pesan'] = "success";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Kategori tidak dapat dihapus karena masih digunakan!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/kategori/index.php");
    exit;
}

header("Location: " . $base_url . "/pages/kategori/index.php");
exit;
