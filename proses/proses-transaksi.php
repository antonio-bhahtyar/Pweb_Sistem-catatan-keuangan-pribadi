<?php
// proses/proses-transaksi.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ====================== TAMBAH TRANSAKSI ======================
if (isset($_POST['tambah_transaksi'])) {
    $tipe        = $_POST['tipe'] ?? '';
    $kategori_id = $_POST['kategori_id'] ?? '';
    $jumlah      = $_POST['jumlah'] ?? '';
    $tanggal     = $_POST['tanggal'] ?? '';
    $keterangan  = trim($_POST['keterangan'] ?? '');

    // Validasi server-side
    if (!in_array($tipe, ['pemasukan', 'pengeluaran'], true)) {
        $_SESSION['pesan'] = "Tipe transaksi tidak valid!";
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: " . $base_url . "/pages/transaksi/add.php"); exit;
    }
    if (!is_numeric($jumlah) || (float)$jumlah <= 0) {
        $_SESSION['pesan'] = "Jumlah harus angka lebih besar dari 0!";
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: " . $base_url . "/pages/transaksi/add.php"); exit;
    }
    $d = DateTime::createFromFormat('Y-m-d', $tanggal);
    if (!$d || $d->format('Y-m-d') !== $tanggal) {
        $_SESSION['pesan'] = "Tanggal tidak valid!";
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: " . $base_url . "/pages/transaksi/add.php"); exit;
    }

    try {
        $check = $pdo->prepare("SELECT id FROM kategori WHERE id = ? AND user_id = ?");
        $check->execute([$kategori_id, $user_id]);
        if (!$check->fetch()) {
            $_SESSION['pesan'] = "Kategori tidak valid!";
            $_SESSION['tipe_pesan'] = "danger";
            header("Location: " . $base_url . "/pages/transaksi/add.php");
            exit;
        }

        $bukti = null;
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $target_dir = __DIR__ . "/../uploads/transaksi/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
            $file_name = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_file = $target_dir . $file_name;

            if (in_array($file_ext, ['jpg','jpeg','png','gif'])) {
                if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
                    $bukti = $file_name;
                }
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO transaksi (user_id, kategori_id, tipe, jumlah, mata_uang, tanggal, keterangan, bukti)
            VALUES (?, ?, ?, ?, 'IDR', ?, ?, ?)
        ");
        $stmt->execute([$user_id, $kategori_id, $tipe, $jumlah, $tanggal, $keterangan, $bukti]);

        $_SESSION['pesan'] = "Transaksi berhasil ditambahkan!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal menambahkan transaksi!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/transaksi/index.php");
    exit;
}

// ====================== EDIT TRANSAKSI ======================
if (isset($_POST['edit_transaksi'])) {
    $id          = $_POST['id'] ?? 0;
    $tipe        = $_POST['tipe'] ?? '';
    $kategori_id = $_POST['kategori_id'] ?? '';
    $jumlah      = $_POST['jumlah'] ?? '';
    $tanggal     = $_POST['tanggal'] ?? '';
    $keterangan  = trim($_POST['keterangan'] ?? '');

    if (!in_array($tipe, ['pemasukan', 'pengeluaran'], true) ||
        !is_numeric($jumlah) || (float)$jumlah <= 0 ||
        !DateTime::createFromFormat('Y-m-d', $tanggal)) {
        $_SESSION['pesan'] = "Data transaksi tidak valid!";
        $_SESSION['tipe_pesan'] = "danger";
        header("Location: " . $base_url . "/pages/transaksi/index.php"); exit;
    }

    try {
        $check = $pdo->prepare("SELECT bukti FROM transaksi WHERE id = ? AND user_id = ?");
        $check->execute([$id, $user_id]);
        $old_data = $check->fetch();

        if (!$old_data) {
            $_SESSION['pesan'] = "Transaksi tidak ditemukan!";
            $_SESSION['tipe_pesan'] = "danger";
            header("Location: " . $base_url . "/pages/transaksi/index.php");
            exit;
        }

        $bukti = $old_data['bukti'];
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $target_dir = __DIR__ . "/../uploads/transaksi/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
            $file_name = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_file = $target_dir . $file_name;

            if (in_array($file_ext, ['jpg','jpeg','png','gif'])) {
                if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
                    if ($old_data['bukti']) {
                        $old_file = $target_dir . $old_data['bukti'];
                        if (file_exists($old_file)) unlink($old_file);
                    }
                    $bukti = $file_name;
                }
            }
        }

        $stmt = $pdo->prepare("
            UPDATE transaksi SET
            tipe = ?, kategori_id = ?, jumlah = ?, tanggal = ?,
            keterangan = ?, bukti = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$tipe, $kategori_id, $jumlah, $tanggal, $keterangan, $bukti, $id, $user_id]);

        $_SESSION['pesan'] = "Transaksi berhasil diupdate!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengupdate transaksi!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/transaksi/index.php");
    exit;
}

// ====================== HAPUS TRANSAKSI ======================
if (isset($_POST['hapus_transaksi'])) {
    $id = $_POST['id'];

    try {
        $check = $pdo->prepare("SELECT bukti FROM transaksi WHERE id = ? AND user_id = ?");
        $check->execute([$id, $user_id]);
        $transaksi = $check->fetch();

        if (!$transaksi) {
            $_SESSION['pesan'] = "Transaksi tidak ditemukan!";
            $_SESSION['tipe_pesan'] = "danger";
            header("Location: " . $base_url . "/pages/transaksi/index.php");
            exit;
        }

        if ($transaksi['bukti']) {
            $file_path = __DIR__ . "/../uploads/transaksi/" . $transaksi['bukti'];
            if (file_exists($file_path)) unlink($file_path);
        }

        $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);

        $_SESSION['pesan'] = "Transaksi berhasil dihapus!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal menghapus transaksi!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/transaksi/index.php");
    exit;
}

// Tidak ada aksi yang valid
header("Location: " . $base_url . "/pages/transaksi/index.php");
exit;
