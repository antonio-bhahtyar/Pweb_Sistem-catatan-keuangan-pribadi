<?php
// pages/transaksi/index.php
$judul_halaman = "Daftar Transaksi";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Filter
$tipe_filter   = $_GET['tipe'] ?? '';
$tgl_mulai     = $_GET['tgl_mulai'] ?? '';
$tgl_selesai   = $_GET['tgl_selesai'] ?? '';
$kategori_filter = $_GET['kategori_id'] ?? '';

$where = "WHERE t.user_id = ?";
$params = [$user_id];

if ($tipe_filter) {
    $where .= " AND t.tipe = ?";
    $params[] = $tipe_filter;
}
if ($tgl_mulai) {
    $where .= " AND t.tanggal >= ?";
    $params[] = $tgl_mulai;
}
if ($tgl_selesai) {
    $where .= " AND t.tanggal <= ?";
    $params[] = $tgl_selesai;
}
if ($kategori_filter) {
    $where .= " AND t.kategori_id = ?";
    $params[] = $kategori_filter;
}

// Ambil transaksi
$stmt = $pdo->prepare("
    SELECT t.*, k.nama_kategori, k.warna
    FROM transaksi t
    JOIN kategori k ON t.kategori_id = k.id
    $where
    ORDER BY t.tanggal DESC, t.id DESC
");
$stmt->execute($params);
$transaksis = $stmt->fetchAll();

// Kategori untuk filter
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY tipe, nama_kategori");
$stmt->execute([$user_id]);
$kategoris = $stmt->fetchAll();
?>

<div class="container-fluid">
    <!-- Flash Message -->
    <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe_pesan'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['pesan'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe_pesan']); endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Transaksi</h4>
        <a href="add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Transaksi</a>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <select name="tipe" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="pemasukan" <?= $tipe_filter == 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                        <option value="pengeluaran" <?= $tipe_filter == 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $kategori_filter == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kategori']) ?> (<?= $k['tipe'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>" placeholder="Dari">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>" placeholder="Sampai">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end">Saldo</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksis)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi.</td>
                    </tr>
                    <?php else: ?>
                    <?php
                    $saldo = 0;
                    $no = 1;
                    foreach ($transaksis as $t):
                        $saldo += ($t['tipe'] == 'pemasukan') ? $t['jumlah'] : -$t['jumlah'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                        <td>
                            <span class="badge" style="background-color: <?= $t['warna'] ?>">
                                <?= htmlspecialchars($t['nama_kategori']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $t['tipe'] == 'pemasukan' ? 'success' : 'danger' ?>">
                                <?= $t['tipe'] ?>
                            </span>
                        </td>
                        <td class="text-end">Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($t['keterangan'] ?? '') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete.php?id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
