<?php
include 'config.php';

if (isset($_GET['board_id'])) {
    $board_id = (int)$_GET['board_id'];

    $stmt = $conn->prepare("DELETE FROM boards WHERE id = ?");
    $stmt->bind_param('i', $board_id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "ID Board tidak ditemukan.";
}
?>
