<?php
session_start();
include 'config.php';  // Koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    // Redirect ke halaman login jika user belum login
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $board_id = (int)$_POST['board_id'];
    $name = htmlspecialchars($_POST['name']);
    
    // Siapkan statement untuk memasukkan data ke dalam tabel lists
    $stmt = $conn->prepare("INSERT INTO lists (board_id, name) VALUES (?, ?)");
    $stmt->bind_param('is', $board_id, $name);

    if ($stmt->execute()) {
        // Redirect ke halaman index setelah berhasil insert
        header("Location: index.php");
    } else {
        // Jika terjadi error, tampilkan pesan error
        echo "Error: " . $stmt->error;
    }
    
    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add New List</h1>
        <form method="POST">
            <input type="hidden" name="board_id" value="<?= $_GET['board_id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">List Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add List</button>
        </form>
    </div>
</body>
</html>


