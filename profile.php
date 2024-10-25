<?php
session_start();
include 'config.php';  // Koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    // Redirect ke halaman login jika user belum login
    header('Location: login.php');
    exit;
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Query untuk mengambil detail pengguna berdasarkan user_id
$stmt = $conn->prepare("SELECT username, email, biodata, tanggal_lahir FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $biodata, $tanggal_lahir);
$stmt->fetch();
$stmt->close();

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-image: url('2.png'); /* Ganti dengan path gambar yang benar */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .container {
            margin-top: 50px;
        }
        .profile-card {
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-card h2 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }
        .profile-card p {
            font-size: 16px;
            color: #666;
        }
        .profile-card .label {
            font-weight: 600;
            color: #555;
        }

        .back-btn i:hover {
            color: black; /* Darker shade for hover effect */
        }

        .back-btn i {
            font-size: 32px;
            font-weight: bold;
            color: purple;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <h2>Profile Information</h2>
            <p><span class="label">Username:</span> <?= htmlspecialchars($username); ?></p>
            <p><span class="label">Email:</span> <?= htmlspecialchars($email); ?></p>
            <p><span class="label">Tanggal Lahir:</span> 
                <?= !empty($tanggal_lahir) ? htmlspecialchars(date('d F Y', strtotime($tanggal_lahir))) : 'Not provided'; ?>
            </p>
            <p><span class="label">Biodata:</span> 
                <?= !empty($biodata) ? htmlspecialchars($biodata) : 'Not provided'; ?>
            </p>

            
            <!-- Tombol Back -->
            <a href="index.php" class="back-btn">                
                <i class="bi bi-arrow-left" style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);"></i>
            </a>

                
            
            
        </div>
    </div>
</body>
</html>
