<?php
$current_role = $_SESSION['role'] ?? 'user';
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
function navActive($dir, $current_dir) {
    return $dir === $current_dir ? 'active fw-bold' : '';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= $base_url ?>/pages/dashboard/index.php">
            <img src="<?= $base_url ?>/assets/images/logo.png" alt="FinanceNote" style="height:32px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= navActive('dashboard', $current_dir) ?>" href="<?= $base_url ?>/pages/dashboard/index.php">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navActive('transaksi', $current_dir) ?>" href="<?= $base_url ?>/pages/transaksi/index.php">
                        <i class="bi bi-arrow-left-right"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navActive('kategori', $current_dir) ?>" href="<?= $base_url ?>/pages/kategori/index.php">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navActive('budget', $current_dir) ?>" href="<?= $base_url ?>/pages/budget/index.php">
                        <i class="bi bi-pie-chart"></i> Anggaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navActive('laporan', $current_dir) ?>" href="<?= $base_url ?>/pages/laporan/index.php">
                        <i class="bi bi-graph-up"></i> Laporan
                    </a>
                </li>

                <?php if ($current_role === 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= navActive('admin', $current_dir) ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-shield-lock"></i> Admin
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/admin/users.php">Kelola User</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        Halo, <strong><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?></strong>
                        <span class="badge bg-<?= $current_role === 'admin' ? 'danger' : 'secondary' ?>">
                            <?= htmlspecialchars(ucfirst($current_role)) ?>
                        </span>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base_url ?>/pages/settings/index.php" title="Pengaturan">
                        <i class="bi bi-gear"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $base_url ?>/proses/auth/logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
