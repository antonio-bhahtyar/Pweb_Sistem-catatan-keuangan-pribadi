<?php
// pages/auth/login.php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/dashboard/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, nama_lengkap, role, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'] ?? 'user';

            $pdo->prepare("UPDATE users SET terakhir_login = NOW() WHERE id = ?")
                 ->execute([$user['id']]);

            header("Location: " . $base_url . "/pages/dashboard/index.php");
            exit;
        } else {
            $error = "Email atau password salah!";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan sistem: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FinanceNote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #6fb3a8 0%, #3d8a82 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-wrapper {
            max-width: 1100px;
            margin: auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .left-side {
            background: linear-gradient(180deg, #6fb3a8 0%, #3d8a82 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .maskot {
            max-width: 280px;
            margin-bottom: 20px;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4));
        }

        .right-side {
            padding: 50px 40px;
        }

        .logo-placeholder {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-img {
            max-width: 200px;
            margin-bottom: 18px;
        }

        .btn-login {
            background: linear-gradient(to right, #6fb3a8, #3d8a82);
            border: none;
            border-radius: 30px;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="login-wrapper row g-0">

            <!-- Kiri: Maskot -->
            <div class="col-lg-6 left-side">
                <img src="<?= $base_url ?>/assets/images/maskot.png" alt="Maskot FinanceNote" class="maskot">
                <h2 class="mb-2">FinanceNote</h2>
                <p class="lead">Catatan Keuangan Pribadi</p>
            </div>

            <!-- Kanan: Form Login -->
            <div class="col-lg-6 right-side">
                <div class="logo-placeholder">
                    <img src="<?= $base_url ?>/assets/images/logo.png" alt="FinanceNote Logo" class="logo-img">
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg" 
                               placeholder="Masukkan email anda" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" 
                                   class="form-control form-control-lg" 
                                   placeholder="Masukkan password anda" required>
                            <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login text-white w-100 mb-3">Login</button>
                </form>

                <div class="text-center mt-3">
                    <p>Belum punya akun? 
                        <a href="register.php" class="fw-bold text-decoration-none">Daftar di sini</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script src="<?= $base_url ?>/assets/js/auth.js"></script>
</body>
</html>