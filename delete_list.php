<?php
include 'config.php';

if (isset($_GET['list_id'])) {
    $list_id = (int)$_GET['list_id'];

    $stmt = $conn->prepare("DELETE FROM lists WHERE id = ?");
    $stmt->bind_param('i', $list_id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "ID List tidak ditemukan.";
}
?>
