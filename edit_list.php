<?php
include 'config.php';

// Cek apakah list_id tersedia
if (isset($_GET['list_id'])) {
    $list_id = (int)$_GET['list_id'];

    // Ambil data list berdasarkan id
    $stmt = $conn->prepare("SELECT * FROM lists WHERE id = ?");
    $stmt->bind_param('i', $list_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = $result->fetch_assoc();

    if (!$list) {
        echo "List tidak ditemukan.";
        exit;
    }

    // Jika form disubmit, update list
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $stmt = $conn->prepare("UPDATE lists SET name = ? WHERE id = ?");
        $stmt->bind_param('si', $name, $list_id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
} else {
    echo "ID List tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit List</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">List Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($list['name']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update List</button>
            <a href="index.php" class="btn btn-secondary ms-2">Back</a>
        </form>
    </div>
</body>
</html>