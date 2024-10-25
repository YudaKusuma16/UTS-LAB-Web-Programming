<?php
session_start();  // Pastikan session dimulai untuk mengambil user_id
include 'config.php';  // Koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    // Redirect ke halaman login jika user belum login
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $user_id = $_SESSION['user_id'];  // Ambil user_id dari session
    
    // Siapkan statement untuk memasukkan data ke dalam tabel boards dengan user_id
    $stmt = $conn->prepare("INSERT INTO boards (user_id, name) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $name);

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
    <title>Add Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add New Board</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Board Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <!-- Wrapper untuk tombol Add Board dan Back -->
            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Add Board</button>
                <a href="index.php" class="btn btn-secondary ms-2">Back</a>
            </div>
        </form>
    </div>
</body>
</html>
