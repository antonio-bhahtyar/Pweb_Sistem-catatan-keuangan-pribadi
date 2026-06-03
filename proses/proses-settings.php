<?php
// proses/proses-settings.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ====================== UPDATE PROFILE ======================
if (isset($_POST['update_profile'])) {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email        = trim($_POST['email']);

    if (empty($nama_lengkap) || empty($email)) {
        $_SESSION['pesan'] = "Nama dan email harus diisi!";
        $_SESSION['tipe_pesan'] = "danger";
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->execute([$email, $user_id]);
            if ($check->rowCount() > 0) {
                $_SESSION['pesan'] = "Email sudah digunakan oleh pengguna lain!";
                $_SESSION['tipe_pesan'] = "danger";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, email = ? WHERE id = ?");
                $stmt->execute([$nama_lengkap, $email, $user_id]);
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['pesan'] = "Profil berhasil diupdate!";
                $_SESSION['tipe_pesan'] = "success";
            }
        } catch (PDOException $e) {
            $_SESSION['pesan'] = "Gagal mengupdate profil!";
            $_SESSION['tipe_pesan'] = "danger";
        }
    }

    header("Location: " . $base_url . "/pages/settings/index.php");
    exit;
}

// ====================== UPDATE PASSWORD ======================
if (isset($_POST['update_password'])) {
    $password_lama  = $_POST['password_lama'];
    $password_baru  = $_POST['password_baru'];
    $password_baru2 = $_POST['password_baru2'];

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($password_lama, $user['password'])) {
            $_SESSION['pesan'] = "Password lama tidak sesuai!";
            $_SESSION['tipe_pesan'] = "danger";
        } elseif ($password_baru !== $password_baru2) {
            $_SESSION['pesan'] = "Konfirmasi password baru tidak cocok!";
            $_SESSION['tipe_pesan'] = "danger";
        } elseif (strlen($password_baru) < 6) {
            $_SESSION['pesan'] = "Password baru minimal 6 karakter!";
            $_SESSION['tipe_pesan'] = "danger";
        } else {
            $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);
            $_SESSION['pesan'] = "Password berhasil diubah!";
            $_SESSION['tipe_pesan'] = "success";
        }
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengubah password!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/settings/index.php");
    exit;
}

// ====================== UPDATE PREFERENCES ======================
if (isset($_POST['update_preferences'])) {
    $mata_uang = $_POST['mata_uang_default'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET mata_uang_default = ? WHERE id = ?");
        $stmt->execute([$mata_uang, $user_id]);
        $_SESSION['pesan'] = "Preferensi berhasil diupdate!";
        $_SESSION['tipe_pesan'] = "success";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengupdate preferensi!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/settings/index.php");
    exit;
}

// ====================== UPDATE PHOTO ======================
if (isset($_POST['update_photo'])) {
    try {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $target_dir = __DIR__ . "/../uploads/profil/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
            $file_name = 'profil_' . $user_id . '_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $file_name;

            if (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                    $old = $pdo->prepare("SELECT foto_profil FROM users WHERE id = ?");
                    $old->execute([$user_id]);
                    $old_photo = $old->fetchColumn();
                    if ($old_photo) {
                        $old_file = $target_dir . $old_photo;
                        if (file_exists($old_file)) unlink($old_file);
                    }

                    $stmt = $pdo->prepare("UPDATE users SET foto_profil = ? WHERE id = ?");
                    $stmt->execute([$file_name, $user_id]);
                    $_SESSION['pesan'] = "Foto profil berhasil diupdate!";
                    $_SESSION['tipe_pesan'] = "success";
                } else {
                    $_SESSION['pesan'] = "Gagal mengupload foto!";
                    $_SESSION['tipe_pesan'] = "danger";
                }
            } else {
                $_SESSION['pesan'] = "Format file tidak didukung (jpg, jpeg, png)!";
                $_SESSION['tipe_pesan'] = "danger";
            }
        } else {
            $_SESSION['pesan'] = "Tidak ada file yang dipilih!";
            $_SESSION['tipe_pesan'] = "warning";
        }
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengupdate foto!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: " . $base_url . "/pages/settings/index.php");
    exit;
}

header("Location: " . $base_url . "/pages/settings/index.php");
exit;
