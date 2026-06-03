<?php
// proses/auth/logout.php
session_start();
require_once __DIR__ . '/../../config/database.php';

session_unset();
session_destroy();

header("Location: " . $base_url . "/pages/auth/login.php");
exit;
?>