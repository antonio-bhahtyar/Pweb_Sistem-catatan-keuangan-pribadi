<?php
// konfigurasi/database.php

$host     = 'localhost';
$dbname   = 'finance_note';
$username = 'root';
$password = '';

// Auto-detect base URL (bisa diakses via localhost/folder atau virtual host)
$doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
$project_root = dirname(__DIR__);
$base_url = str_replace('\\', '/', substr($project_root, strlen($doc_root)));

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Setting agar lebih aman dan nyaman
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("❌ Koneksi Database Gagal: " . $e->getMessage());
}
?>