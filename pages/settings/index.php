<?php
// pages/settings/index.php
$judul_halaman = "Pengaturan";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$tab = $_GET['tab'] ?? 'profil';
?>

<div class="container-fluid">
    <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe_pesan'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['pesan'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe_pesan']); endif; ?>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?= $tab == 'profil' ? 'active' : '' ?>" href="?tab=profil">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $tab == 'password' ? 'active' : '' ?>" href="?tab=password">Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $tab == 'preferensi' ? 'active' : '' ?>" href="?tab=preferensi">Preferensi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $tab == 'foto' ? 'active' : '' ?>" href="?tab=foto">Foto Profil</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">

                    <!-- Tab Profil -->
                    <?php if ($tab == 'profil'): ?>
                    <form action="../../proses/proses-settings.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control form-control-lg"
                                   value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg"
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                    </form>
                    <?php endif; ?>

                    <!-- Tab Password -->
                    <?php if ($tab == 'password'): ?>
                    <form action="../../proses/proses-settings.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control form-control-lg" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control form-control-lg" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru2" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-warning btn-lg">Ganti Password</button>
                    </form>
                    <?php endif; ?>

                    <!-- Tab Preferensi -->
                    <?php if ($tab == 'preferensi'): ?>
                    <form action="../../proses/proses-settings.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Mata Uang Default</label>
                            <select name="mata_uang_default" class="form-select form-select-lg" style="max-width: 300px;">
                                <option value="IDR" <?= $user['mata_uang_default'] == 'IDR' ? 'selected' : '' ?>>IDR - Rupiah</option>
                                <option value="USD" <?= $user['mata_uang_default'] == 'USD' ? 'selected' : '' ?>>USD - Dollar</option>
                                <option value="SGD" <?= $user['mata_uang_default'] == 'SGD' ? 'selected' : '' ?>>SGD - Singapore Dollar</option>
                                <option value="MYR" <?= $user['mata_uang_default'] == 'MYR' ? 'selected' : '' ?>>MYR - Ringgit</option>
                            </select>
                        </div>
                        <button type="submit" name="update_preferences" class="btn btn-primary btn-lg">Simpan</button>
                    </form>
                    <?php endif; ?>

                    <!-- Tab Foto -->
                    <?php if ($tab == 'foto'): ?>
                    <div class="text-center mb-4">
                        <?php if ($user['foto_profil']): ?>
                        <img src="<?= $base_url ?>/uploads/profil/<?= $user['foto_profil'] ?>"
                             class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                        <?php else: ?>
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                             style="width: 150px; height: 150px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 80px;"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <form action="../../proses/proses-settings.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label">Upload Foto Baru</label>
                            <input type="file" name="foto_profil" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maks 2MB.</small>
                        </div>
                        <button type="submit" name="update_photo" class="btn btn-primary btn-lg">Upload Foto</button>
                    </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
