<?php
// test_koneksi.php

require_once 'config/database.php';   // Sesuai nama folder config kamu

echo "<h1>🔍 Tes Koneksi Database FinanceNote</h1>";

if(isset($pdo)) {
    echo "✅ <strong>KONEKSI BERHASIL!</strong><br><br>";
    echo "Database: <strong>finance_note</strong>";
} else {
    echo "❌ Koneksi GAGAL";
}
?>