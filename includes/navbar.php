<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= $base_url ?>/pages/dashboard/index.php">💰 FinanceNote</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        Halo, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a href="<?= $base_url ?>/proses/auth/logout.php" class="btn btn-outline-light btn-sm">Keluar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>