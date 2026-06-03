<?php
// pages/kategori/index.php
$judul_halaman = "Kelola Kategori";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Cek mode edit
$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['edit'], $user_id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) $edit_mode = true;
}

// Ambil semua kategori
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY tipe, nama_kategori");
$stmt->execute([$user_id]);
$kategoris = $stmt->fetchAll();

$pemasukan = array_filter($kategoris, fn($k) => $k['tipe'] == 'pemasukan');
$pengeluaran = array_filter($kategoris, fn($k) => $k['tipe'] == 'pengeluaran');
?>

<div class="container-fluid">
    <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe_pesan'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['pesan'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe_pesan']); endif; ?>

    <div class="row">
        <!-- Form Tambah / Edit -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-<?= $edit_mode ? 'warning' : 'primary' ?> text-white">
                    <h5><?= $edit_mode ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h5>
                </div>
                <div class="card-body">
                    <form action="../../proses/proses-kategori.php" method="POST">
                        <?php if ($edit_mode): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control form-control-lg"
                                   value="<?= $edit_mode ? htmlspecialchars($edit_data['nama_kategori']) : '' ?>"
                                   placeholder="Contoh: Gaji, Makan, Transport" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <div class="btn-group w-100">
                                <input type="radio" name="tipe" id="tpemasukan" value="pemasukan" class="btn-check"
                                    <?= ($edit_mode && $edit_data['tipe'] == 'pemasukan') || !$edit_mode ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="tpemasukan">Pemasukan</label>

                                <input type="radio" name="tipe" id="tpengeluaran" value="pengeluaran" class="btn-check"
                                    <?= ($edit_mode && $edit_data['tipe'] == 'pengeluaran') ? 'checked' : '' ?>>
                                <label class="btn btn-outline-danger" for="tpengeluaran">Pengeluaran</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Warna</label>
                            <input type="color" name="warna" class="form-control form-control-color" style="height: 45px; width: 100%;"
                                   value="<?= $edit_mode ? $edit_data['warna'] : '#6c757d' ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="<?= $edit_mode ? 'update_kategori' : 'tambah_kategori' ?>"
                                    class="btn btn-<?= $edit_mode ? 'warning' : 'primary' ?> btn-lg">
                                <?= $edit_mode ? 'Update Kategori' : 'Simpan Kategori' ?>
                            </button>
                            <?php if ($edit_mode): ?>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Kategori -->
        <div class="col-md-7">
            <?php foreach (['pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran'] as $tipe_key => $tipe_label):
                $list = $tipe_key == 'pemasukan' ? $pemasukan : $pengeluaran;
            ?>
            <div class="card mb-3">
                <div class="card-header bg-<?= $tipe_key == 'pemasukan' ? 'success' : 'danger' ?> text-white">
                    <h5>Kategori <?= $tipe_label ?> (<?= count($list) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($list)): ?>
                    <p class="text-muted">Belum ada kategori <?= $tipe_label ?>.</p>
                    <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($list as $k): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge rounded-circle me-2" style="background-color: <?= $k['warna'] ?>; width: 20px; height: 20px; display: inline-block; vertical-align: middle;">&nbsp;</span>
                                <strong><?= htmlspecialchars($k['nama_kategori']) ?></strong>
                            </div>
                            <div>
                                <a href="index.php?edit=<?= $k['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="<?= $k['id'] ?>"
                                        data-nama="<?= htmlspecialchars($k['nama_kategori']) ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="deleteKategoriNama">-</strong>?</p>
                <p class="text-danger small">Kategori yang sudah digunakan transaksi tidak dapat dihapus.</p>
            </div>
            <div class="modal-footer">
                <form action="../../proses/proses-kategori.php" method="POST">
                    <input type="hidden" name="id" id="deleteKategoriId">
                    <button type="submit" name="hapus_kategori" class="btn btn-danger">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        document.getElementById('deleteKategoriId').value = button.getAttribute('data-id');
        document.getElementById('deleteKategoriNama').textContent = button.getAttribute('data-nama');
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
