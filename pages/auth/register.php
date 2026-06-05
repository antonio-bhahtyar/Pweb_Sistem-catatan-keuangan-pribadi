<?php
// pages/auth/register.php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/dashboard/index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if (empty($nama_lengkap) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $password2) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $error = "Email sudah terdaftar!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$nama_lengkap, $email, $hashed_password]);

                $success = "Pendaftaran berhasil! Silakan login.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - FinanceNote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #6fb3a8 0%, #3d8a82 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .register-card {
            max-width: 480px;
            margin: auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(to right, #6fb3a8, #3d8a82);
            color: white;
            padding: 35px 30px;
            text-align: center;
        }

        .card-header .logo-img {
            max-width: 180px;
            margin-bottom: 12px;
        }

        .btn-register {
            background: linear-gradient(to right, #6fb3a8, #3d8a82);
            border: none;
            border-radius: 30px;
            padding: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="register-card">

            <!-- Header -->
            <div class="card-header">
                <img src="<?= $base_url ?>/assets/images/logo.png" alt="FinanceNote Logo" class="logo-img">
                <h3 class="mb-1">Buat Akun Baru</h3>
                <p class="mb-0">Mulai kelola keuangan pribadimu</p>
            </div>

            <div class="card-body p-5">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" id="password" class="form-control form-control-lg"
                               placeholder="Minimal 6 karakter" required>
                        <div class="progress mt-1" style="height: 5px;">
                            <div id="passwordStrength" class="progress-bar" style="width: 0%;"></div>
                        </div>
                        <small id="passwordStrengthText" class="text-muted"></small>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input type="password" name="password2" id="password2" class="form-control form-control-lg" required>
                        <small id="passwordMatchMsg"></small>
                    </div>

                    <button type="submit" class="btn btn-register text-white w-100">Daftar Sekarang</button>
                </form>

                <div class="text-center mt-4">
                    <p>Sudah punya akun? 
                        <a href="login.php" class="fw-bold text-decoration-none">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $base_url ?>/assets/js/auth.js"></script>
</body>
</html>