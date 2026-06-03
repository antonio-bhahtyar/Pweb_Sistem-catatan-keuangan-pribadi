<?php
// pages/transaksis/edit.php
$judul_halaman = "Edit Transaksi";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

// Ambil data transaksi
$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    echo "<div class='alert alert-danger text-center'>Transaksi tidak ditemukan!</div>";
    exit;
}

// Ambil daftar kategori
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY tipe, nama_kategori");
$stmt->execute([$user_id]);
$kategoris = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5>Edit Transaksi</h5>
                </div>
                <div class="card-body">
                    <form action="../../proses/proses-transaksi.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $transaksi['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Tipe Transaksi</label>
                            <div class="btn-group w-100">
                                <input type="radio" name="tipe" id="pemasukan" value="pemasukan" class="btn-check" <?= $transaksi['tipe'] == 'pemasukan' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="pemasukan">Pemasukan</label>
                                
                                <input type="radio" name="tipe" id="pengeluaran" value="pengeluaran" class="btn-check" <?= $transaksi['tipe'] == 'pengeluaran' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-danger" for="pengeluaran">Pengeluaran</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select form-select-lg" required>
                                <?php foreach($kategoris as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $k['id'] == $transaksi['kategori_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kategori']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control form-control-lg" 
                                   value="<?= $transaksi['jumlah'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-lg" 
                                   value="<?= $transaksi['tanggal'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($transaksi['keterangan'] ?? '') ?></textarea>
                        </div>

                        <?php if($transaksi['bukti']): ?>
                        <div class="mb-3">
                            <label class="form-label">Bukti Saat Ini</label><br>
                            <img src="../../uploads/transaksi/<?= $transaksi['bukti'] ?>" width="200" class="img-thumbnail">
                        </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="form-label">Ganti Bukti (Opsional)</label>
                            <input type="file" name="bukti" class="form-control" accept="image/*">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="edit_transaksi" class="btn btn-warning btn-lg">Simpan Perubahan</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>