<?php
// pages/transaksis/add.php
$judul_halaman = "Tambah Transaksi";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Ambil kategori untuk dropdown
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE user_id = ? ORDER BY tipe, nama_kategori");
$stmt->execute([$user_id]);
$kategoris = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Tambah Transaksi Baru</h5>
                </div>
                <div class="card-body">
                    <form action="../../proses/proses-transaksi.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label">Tipe Transaksi</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" name="tipe" id="pemasukan" value="pemasukan" class="btn-check" checked>
                                <label class="btn btn-outline-success" for="pemasukan">Pemasukan</label>
                                
                                <input type="radio" name="tipe" id="pengeluaran" value="pengeluaran" class="btn-check">
                                <label class="btn btn-outline-danger" for="pengeluaran">Pengeluaran</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select form-select-lg" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach($kategoris as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?> (<?= $k['tipe'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control form-control-lg" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Bukti Transaksi (Opsional)</label>
                            <input type="file" name="bukti" class="form-control" accept="image/*">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="tambah_transaksi" class="btn btn-primary btn-lg">Simpan Transaksi</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>