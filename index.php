<?php
// index.php
session_start();
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/dashboard/index.php");
} else {
    header("Location: " . $base_url . "/pages/auth/login.php");
}
exit;
?>