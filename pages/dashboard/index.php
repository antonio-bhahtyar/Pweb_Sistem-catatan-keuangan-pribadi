<?php
// pages/dashboard/index.php
$judul_halaman = "Dashboard";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Total saldo (semua waktu)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) -
           COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) as saldo
    FROM transaksi WHERE user_id = ?
");
$stmt->execute([$user_id]);
$saldo = $stmt->fetchColumn();

// Pemasukan bulan ini
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(jumlah), 0) FROM transaksi
    WHERE user_id = ? AND tipe = 'pemasukan'
      AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
");
$stmt->execute([$user_id]);
$pemasukan = $stmt->fetchColumn();

// Pengeluaran bulan ini
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(jumlah), 0) FROM transaksi
    WHERE user_id = ? AND tipe = 'pengeluaran'
      AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
");
$stmt->execute([$user_id]);
$pengeluaran = $stmt->fetchColumn();
?>

<div class="container py-4">
    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>!</h2>
    <p class="text-muted">Kelola keuangan pribadi Anda dengan mudah dan rapi.</p>

    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h5>Total Saldo</h5>
                    <h3>Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h5>Pemasukan Bulan Ini</h5>
                    <h3>Rp <?= number_format($pemasukan, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body">
                    <h5>Pengeluaran Bulan Ini</h5>
                    <h3>Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>