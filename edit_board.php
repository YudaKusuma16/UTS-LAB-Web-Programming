<?php
include 'config.php'; // Koneksi ke database

// Cek apakah board_id tersedia di URL
if (isset($_GET['board_id'])) {
    $board_id = (int)$_GET['board_id'];

    // Ambil data board berdasarkan id yang diberikan
    $stmt = $conn->prepare("SELECT * FROM boards WHERE id = ?");
    $stmt->bind_param('i', $board_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $board = $result->fetch_assoc();

    // Cek apakah board ditemukan
    if (!$board) {
        echo "Board tidak ditemukan.";
        exit();
    }

    // Jika form disubmit, update nama board
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars($_POST['name']); // Sanitize input untuk menghindari XSS
        $stmt = $conn->prepare("UPDATE boards SET name = ? WHERE id = ?");
        $stmt->bind_param('si', $name, $board_id);

        // Eksekusi query update
        if ($stmt->execute()) {
            // Jika sukses, redirect ke halaman index.php
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
} else {
    echo "ID Board tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Board</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Board</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($board['name']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Board</button>
            <a href="index.php" class="btn btn-secondary ms-2">Back</a>
        </form>
    </div>
</body>
</html>



