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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-dark text-white p-3" style="min-height: 100vh;">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="index.php" class="nav-link active text-white"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="../transaksi/" class="nav-link text-white"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
                <li class="nav-item"><a href="../kategori/" class="nav-link text-white"><i class="fas fa-tags"></i> Kategori</a></li>
                <li class="nav-item"><a href="../budget/" class="nav-link text-white"><i class="fas fa-chart-bar"></i> Anggaran</a></li>
                <li class="nav-item"><a href="../laporan/" class="nav-link text-white"><i class="fas fa-file-alt"></i> Laporan</a></li>
                <li class="nav-item"><a href="../settings/" class="nav-link text-white"><i class="fas fa-cog"></i> Pengaturan</a></li>
                <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                <li class="nav-item mt-3"><hr class="border-secondary"></li>
                <li class="nav-item"><small class="text-muted ms-3">ADMIN PANEL</small></li>
                <li class="nav-item"><a href="../admin/users.php" class="nav-link text-white"><i class="fas fa-users"></i> Kelola User</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>!</h2>
            <p class="text-muted">Kelola keuangan pribadi Anda dengan mudah dan rapi.</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5>Total Saldo</h5>
                            <h3>Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5>Pemasukan Bulan Ini</h5>
                            <h3>Rp <?= number_format($pemasukan, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5>Pengeluaran Bulan Ini</h5>
                            <h3>Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>