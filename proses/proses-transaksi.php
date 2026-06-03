// ====================== EDIT TRANSAKSI ======================
if (isset($_POST['edit_transaksi'])) {
    
    $id            = $_POST['id'];
    $tipe          = $_POST['tipe'];
    $kategori_id   = $_POST['kategori_id'];
    $jumlah        = $_POST['jumlah'];
    $tanggal       = $_POST['tanggal'];
    $keterangan    = trim($_POST['keterangan'] ?? '');

    try {
        // Cek apakah transaksi milik user ini
        $check = $pdo->prepare("SELECT bukti FROM transaksi WHERE id = ? AND user_id = ?");
        $check->execute([$id, $user_id]);
        $old_data = $check->fetch();

        if (!$old_data) {
            $_SESSION['pesan'] = "Transaksi tidak ditemukan!";
            $_SESSION['tipe_pesan'] = "danger";
            header("Location: ../pages/transaksis/index.php");
            exit;
        }

        // Upload bukti baru jika ada
        $bukti = $old_data['bukti'];
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $target_dir = "../uploads/transaksi/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
            $file_name = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_file = $target_dir . $file_name;

            if (in_array($file_ext, ['jpg','jpeg','png','gif'])) {
                if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
                    // Hapus bukti lama
                    if ($old_data['bukti']) {
                        $old_file = $target_dir . $old_data['bukti'];
                        if (file_exists($old_file)) unlink($old_file);
                    }
                    $bukti = $file_name;
                }
            }
        }

        $stmt = $pdo->prepare("
            UPDATE transaksi SET 
            tipe = ?, kategori_id = ?, jumlah = ?, tanggal = ?, 
            keterangan = ?, bukti = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        
        $stmt->execute([$tipe, $kategori_id, $jumlah, $tanggal, $keterangan, $bukti, $id, $user_id]);

        $_SESSION['pesan'] = "Transaksi berhasil diupdate!";
        $_SESSION['tipe_pesan'] = "success";

    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal mengupdate transaksi!";
        $_SESSION['tipe_pesan'] = "danger";
    }

    header("Location: ../pages/transaksis/index.php");
    exit;
}