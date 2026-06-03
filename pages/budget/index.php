<?php
// pages/budget/index.php
$judul_halaman = "Kelola Anggaran";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Bulan & tahun
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Data anggaran
$stmt = $pdo->prepare("
    SELECT a.*, k.nama_kategori, k.warna,
           COALESCE(SUM(t.jumlah), 0) as total_pengeluaran
    FROM anggaran a
    JOIN kategori k ON a.kategori_id = k.id
    LEFT JOIN transaksi t ON t.kategori_id = a.kategori_id
        AND t.tipe = 'pengeluaran' AND t.user_id = a.user_id
        AND MONTH(t.tanggal) = a.bulan AND YEAR(t.tanggal) = a.tahun
    WHERE a.user_id = ? AND a.bulan = ? AND a.tahun = ?
    GROUP BY a.id, k.nama_kategori, k.warna
    ORDER BY k.nama_kategori
");
$stmt->execute([$user_id, $bulan, $tahun]);
$budgets = $stmt->fetchAll();

// Kategori yang sudah dianggarkan bulan ini
$used_ids = array_column($budgets, 'kategori_id');

// Kategori pengeluaran yang belum dianggarkan
$placeholders = implode(',', array_fill(0, count($used_ids) + 1, '?'));
$params = array_merge([$user_id], $used_ids);
$stmt = $pdo->prepare("
    SELECT * FROM kategori
    WHERE user_id = ? AND tipe = 'pengeluaran'
    " . ($used_ids ? "AND id NOT IN (" . implode(',', array_fill(0, count($used_ids), '?')) . ")" : "") . "
    ORDER BY nama_kategori
");
$stmt->execute($params);
$available_kategoris = $stmt->fetchAll();
?>

<div class="container-fluid">
    <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe_pesan'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['pesan'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe_pesan']); endif; ?>

    <!-- Selector Bulan -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php foreach ($nama_bulan as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>"
                           min="2020" max="<?= date('Y') + 1 ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lihat</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Anggaran -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Anggaran <?= $nama_bulan[$bulan] ?> <?= $tahun ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($budgets)): ?>
                    <p class="text-muted text-center py-3">Belum ada anggaran untuk bulan ini.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-end">Anggaran</th>
                                    <th class="text-end">Pengeluaran</th>
                                    <th class="text-end">Sisa</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($budgets as $b):
                                    $persen = $b['jumlah'] > 0 ? round(($b['total_pengeluaran'] / $b['jumlah']) * 100) : 0;
                                    $sisa = $b['jumlah'] - $b['total_pengeluaran'];
                                    $bar_class = $persen >= 100 ? 'bg-danger' : ($persen >= 80 ? 'bg-warning' : 'bg-success');
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: <?= $b['warna'] ?>">
                                            <?= htmlspecialchars($b['nama_kategori']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($b['jumlah'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($b['total_pengeluaran'], 0, ',', '.') ?></td>
                                    <td class="text-end <?= $sisa < 0 ? 'text-danger fw-bold' : '' ?>">
                                        Rp <?= number_format($sisa, 0, ',', '.') ?>
                                    </td>
                                    <td style="min-width: 120px;">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?= $bar_class ?>" style="width: <?= min($persen, 100) ?>%">
                                                <?= $persen ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editBudgetModal"
                                                data-id="<?= $b['id'] ?>"
                                                data-jumlah="<?= $b['jumlah'] ?>"
                                                data-kategori="<?= htmlspecialchars($b['nama_kategori']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteBudgetModal"
                                                data-id="<?= $b['id'] ?>"
                                                data-kategori="<?= htmlspecialchars($b['nama_kategori']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Form Tambah -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>Tambah Anggaran</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($available_kategoris)): ?>
                    <p class="text-muted">Semua kategori pengeluaran sudah dianggarkan bulan ini.</p>
                    <?php else: ?>
                    <form action="../../proses/proses-budget.php" method="POST">
                        <input type="hidden" name="bulan" value="<?= $bulan ?>">
                        <input type="hidden" name="tahun" value="<?= $tahun ?>">
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($available_kategoris as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Anggaran (Rp)</label>
                            <input type="number" name="jumlah" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" name="tambah_budget" class="btn btn-success w-100">Simpan</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Budget -->
<div class="modal fade" id="editBudgetModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Anggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../../proses/proses-budget.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editBudgetId">
                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                    <input type="hidden" name="tahun" value="<?= $tahun ?>">
                    <p>Kategori: <strong id="editBudgetKategori">-</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="editBudgetJumlah" class="form-control form-control-lg" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_budget" class="btn btn-warning">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Budget -->
<div class="modal fade" id="deleteBudgetModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hapus Anggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../../proses/proses-budget.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="deleteBudgetId">
                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                    <input type="hidden" name="tahun" value="<?= $tahun ?>">
                    <p>Hapus anggaran <strong id="deleteBudgetKategori">-</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="hapus_budget" class="btn btn-danger">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editModal = document.getElementById('editBudgetModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        var btn = event.relatedTarget;
        document.getElementById('editBudgetId').value = btn.getAttribute('data-id');
        document.getElementById('editBudgetJumlah').value = btn.getAttribute('data-jumlah');
        document.getElementById('editBudgetKategori').textContent = btn.getAttribute('data-kategori');
    });

    var deleteModal = document.getElementById('deleteBudgetModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        var btn = event.relatedTarget;
        document.getElementById('deleteBudgetId').value = btn.getAttribute('data-id');
        document.getElementById('deleteBudgetKategori').textContent = btn.getAttribute('data-kategori');
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
