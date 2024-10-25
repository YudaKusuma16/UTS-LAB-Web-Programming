<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = (int)$_POST['task_id'];
    $status = htmlspecialchars($_POST['status']); // 'incomplete' atau 'complete'

    // Query untuk update status task
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $task_id);

    if ($stmt->execute()) {
        // Redirect ke halaman utama setelah status berhasil diupdate
        header('Location: index.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>


