<?php
// konfigurasi/database.php

$host     = 'localhost';
$dbname   = 'finance_note';      // Pastikan nama database ini sama
$username = 'root';              // Default Laragon
$password = '';                  // Kosongkan jika pakai Laragon default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Setting agar lebih aman dan nyaman
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("❌ Koneksi Database Gagal: " . $e->getMessage());
}
?>