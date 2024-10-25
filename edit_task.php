<?php
include 'config.php';

// Cek apakah task_id tersedia
if (isset($_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];

    // Ambil data task berdasarkan id
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->bind_param('i', $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if (!$task) {
        echo "Task tidak ditemukan.";
        exit;
    }

    // Jika form disubmit, update task
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $description = htmlspecialchars($_POST['description']);
        $due_date = htmlspecialchars($_POST['due_date']);
        $category = htmlspecialchars($_POST['category']);

        $stmt = $conn->prepare("UPDATE tasks SET description = ?, due_date = ?, category = ? WHERE id = ?");
        $stmt->bind_param('sssi', $description, $due_date, $category, $task_id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
} else {
    echo "ID Task tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Task</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="description" class="form-label">Task Description</label>
                <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($task['description']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="<?= htmlspecialchars($task['due_date']); ?>">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($task['category']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Task</button>
            <a href="index.php" class="btn btn-secondary ms-2">Back</a>
        </form>
    </div>
</body>
</html>
