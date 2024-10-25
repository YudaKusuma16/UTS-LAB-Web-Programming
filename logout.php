<?php
session_start(); // Mulai sesi

// Hapus semua variabel sesi
$_SESSION = [];

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login atau halaman utama setelah logout
header("Location: login.php"); // Ubah ke halaman login yang sesuai
exit;
?>
