<?php
// pages/admin/users.php
$judul_halaman = "Kelola User";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

// Hanya admin yang bisa akses
if (($_SESSION['role'] ?? 'user') !== 'admin') {
    header("Location: " . $base_url . "/pages/dashboard/index.php");
    exit;
}

// Ambil semua user
$stmt = $pdo->query("
    SELECT u.*,
           COUNT(t.id) as total_transaksi
    FROM users u
    LEFT JOIN transaksi t ON t.user_id = u.id
    GROUP BY u.id
    ORDER BY u.tanggal_daftar DESC
");
$users = $stmt->fetchAll();
?>

<div class="container-fluid">
    <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe_pesan'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['pesan'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe_pesan']); endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Kelola User</h4>
    </div>

    <div class="row mb-4">
        <!-- Statistik -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6>Total User</h6>
                    <h2><?= count($users) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6>Admin</h6>
                    <h2><?= count(array_filter($users, fn($u) => ($u['role'] ?? 'user') === 'admin')) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5>Daftar User</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Total Transaksi</th>
                        <th>Terakhir Login</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada user.</td></tr>
                    <?php else: $no = 1; foreach ($users as $u): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($u['nama_lengkap']) ?></strong>
                            <?php if ($u['id'] == $_SESSION['user_id']): ?>
                            <span class="badge bg-info">Anda</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= ($u['role'] ?? 'user') === 'admin' ? 'danger' : 'secondary' ?>">
                                <?= ucfirst($u['role'] ?? 'user') ?>
                            </span>
                        </td>
                        <td class="text-center"><?= $u['total_transaksi'] ?></td>
                        <td><?= $u['terakhir_login'] ? date('d/m/Y H:i', strtotime($u['terakhir_login'])) : '-' ?></td>
                        <td><?= date('d/m/Y', strtotime($u['tanggal_daftar'])) ?></td>
                        <td>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <div class="d-flex gap-1">
                                <!-- Toggle Role -->
                                <form action="../../proses/proses-admin.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" name="toggle_role" class="btn btn-warning btn-sm" title="Ganti Role">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <!-- Delete -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        data-id="<?= $u['id'] ?>"
                                        data-nama="<?= htmlspecialchars($u['nama_lengkap']) ?>"
                                        title="Hapus User">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Hapus User -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Peringatan!</strong> Semua data user ini (transaksi, kategori, anggaran) akan ikut terhapus.
                </div>
                <p>Hapus user <strong id="deleteUserNama">-</strong>?</p>
            </div>
            <div class="modal-footer">
                <form action="../../proses/proses-admin.php" method="POST">
                    <input type="hidden" name="id" id="deleteUserId">
                    <button type="submit" name="hapus_user" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var deleteModal = document.getElementById('deleteUserModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        var btn = event.relatedTarget;
        document.getElementById('deleteUserId').value = btn.getAttribute('data-id');
        document.getElementById('deleteUserNama').textContent = btn.getAttribute('data-nama');
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
