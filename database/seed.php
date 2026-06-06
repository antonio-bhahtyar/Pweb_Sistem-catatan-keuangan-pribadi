<?php
// database/seed.php
// Jalankan SEKALI setelah import finance_note.sql untuk mengisi hash password seeder
// Akses: http://localhost/FinanceNote/database/seed.php
require_once __DIR__ . '/../config/database.php';

$default_password = 'password123';
$hash = password_hash($default_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE password = '__PLACEHOLDER__'");
    $stmt->execute([$hash]);
    $n = $stmt->rowCount();
    echo "<pre>Seeder selesai. {$n} user di-update dengan password default: '{$default_password}'\n\n";
    echo "Akun demo:\n";
    echo "  Admin -> admin@financenote.test / {$default_password}\n";
    echo "  User  -> budi@financenote.test  / {$default_password}\n";
    echo "</pre>";
} catch (PDOException $e) {
    die("Seeder gagal: " . htmlspecialchars($e->getMessage()));
}
