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

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $biodata = htmlspecialchars($_POST['biodata']);
    $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir']);

    // Update data tanpa mengubah password jika kosong
    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, biodata = ?, tanggal_lahir = ? WHERE id = ?");
        $stmt->bind_param('sssssi', $username, $email, $password, $biodata, $tanggal_lahir, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, biodata = ?, tanggal_lahir = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $username, $email, $biodata, $tanggal_lahir, $user_id);
    }

    if ($stmt->execute()) {
        // Update session variables with new username and email
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
    
        $_SESSION['success'] = "Profile updated successfully!";
        header('Location: edit_profile.php');  // Redirect to the same page to clear POST data
        exit;
    } else {
        $error = "Failed to update profile.";
    }
    $stmt->close();
}

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
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Red Hat Display', sans-serif;
            margin: 0;
        }
        .container {
            margin-top: 50px;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .back-btn {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #0056b3;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <!-- Notifikasi sukses update profil -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); // Hapus session success setelah ditampilkan ?>
        <?php endif; ?>

        <!-- Notifikasi error jika ada masalah saat update -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST">
            <!-- Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($username); ?>" required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>" required>
            </div>

            <!-- Password (Opsional) -->
            <div class="form-group">
                <label for="password">Password (leave blank if not changing)</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>

            <!-- Biodata -->
            <div class="form-group">
                <label for="biodata">Biodata</label>
                <textarea id="biodata" name="biodata" class="form-control" rows="4"><?= htmlspecialchars($biodata); ?></textarea>
            </div>

            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($tanggal_lahir); ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update Profile</button>

            <!-- Back Button -->
            <a href="index.php" class="back-btn">Back</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
