<?php
include 'config.php';

if (isset($_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param('i', $task_id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "ID Task tidak ditemukan.";
}
?>
