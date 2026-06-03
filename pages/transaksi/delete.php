<?php
// pages/transaksi/delete.php
$judul_halaman = "Hapus Transaksi";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT t.*, k.nama_kategori
    FROM transaksi t
    JOIN kategori k ON t.kategori_id = k.id
    WHERE t.id = ? AND t.user_id = ?
");
$stmt->execute([$id, $user_id]);
$transaksi = $stmt->fetch();

if (!$transaksi):
?>
<div class="container-fluid">
    <div class="alert alert-danger text-center">
        <h5>Transaksi tidak ditemukan!</h5>
        <a href="index.php" class="btn btn-secondary mt-2">Kembali</a>
    </div>
</div>
<?php else: ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5><i class="bi bi-exclamation-triangle"></i> Hapus Transaksi?</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.
                    </div>

                    <table class="table table-bordered">
                        <tr><th width="150">Tanggal</th><td><?= date('d/m/Y', strtotime($transaksi['tanggal'])) ?></td></tr>
                        <tr><th>Tipe</th><td><?= ucfirst($transaksi['tipe']) ?></td></tr>
                        <tr><th>Kategori</th><td><?= htmlspecialchars($transaksi['nama_kategori']) ?></td></tr>
                        <tr><th>Jumlah</th><td>Rp <?= number_format($transaksi['jumlah'], 0, ',', '.') ?></td></tr>
                        <?php if ($transaksi['keterangan']): ?>
                        <tr><th>Keterangan</th><td><?= htmlspecialchars($transaksi['keterangan']) ?></td></tr>
                        <?php endif; ?>
                    </table>

                    <form action="../../proses/proses-transaksi.php" method="POST">
                        <input type="hidden" name="id" value="<?= $transaksi['id'] ?>">
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" name="hapus_transaksi" class="btn btn-danger btn-lg">
                                Ya, Hapus Transaksi
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
