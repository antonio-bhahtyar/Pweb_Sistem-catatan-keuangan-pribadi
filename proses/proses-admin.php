<?php
// proses/proses-admin.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header("Location: " . $base_url . "/pages/dashboard/index.php");
    exit;
}

// ====================== TOGGLE ROLE ======================
if (isset($_POST['toggle_role'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            $new_role = ($user['role'] === 'admin') ? 'user' : 'admin';
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $id]);
            $_SESSION['pesan'] = "Role user berhasil diubah menjadi " . $new_role . "!";
            $_SESSION['tipe_pesan'] = "success";
        }
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengubah role!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/admin/users.php");
    exit;
}

// ====================== HAPUS USER ======================
if (isset($_POST['hapus_user'])) {
    $id = $_POST['id'];

    // Admin tidak bisa hapus diri sendiri
    if ($id == $_SESSION['user_id']) {
        $_SESSION['pesan'] = "Anda tidak dapat menghapus akun sendiri!";
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: " . $base_url . "/pages/admin/users.php");
        exit;
    }

    try {
        // Hapus file bukti transaksi user
        $stmt = $pdo->prepare("SELECT bukti FROM transaksi WHERE user_id = ? AND bukti IS NOT NULL");
        $stmt->execute([$id]);
        $files = $stmt->fetchAll();

        foreach ($files as $f) {
            $path = __DIR__ . "/../uploads/transaksi/" . $f['bukti'];
            if (file_exists($path)) unlink($path);
        }

        // Hapus foto profil
        $stmt = $pdo->prepare("SELECT foto_profil FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if ($user['foto_profil']) {
            $path = __DIR__ . "/../uploads/profil/" . $user['foto_profil'];
            if (file_exists($path)) unlink($path);
        }

        // Hapus user (cascade akan hapus transaksi, kategori, anggaran)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['pesan'] = "User berhasil dihapus beserta seluruh datanya!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal menghapus user!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/admin/users.php");
    exit;
}

header("Location: " . $base_url . "/pages/admin/users.php");
exit;
