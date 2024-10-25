<?php
$host = 'localhost'; // atau nama host dari database server
$user = 'root'; // username MySQL
$password = ''; // password MySQL
$dbname = 'taskly'; // nama database

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
