<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $list_id = (int)$_POST['list_id'];
    $description = htmlspecialchars($_POST['description']);
    $due_date = htmlspecialchars($_POST['due_date']);
    $category = htmlspecialchars($_POST['category']);
    $status = 'incomplete'; // Default status

    $stmt = $conn->prepare("INSERT INTO tasks (list_id, description, due_date, category, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $list_id, $description, $due_date, $category, $status);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add New Task</h1>
        <form method="POST">
            <input type="hidden" name="list_id" value="<?= $_GET['list_id']; ?>">
            <div class="mb-3">
                <label for="description" class="form-label">Task Description</label>
                <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
    </div>
</body>
</html>
