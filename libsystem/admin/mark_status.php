<?php
include 'includes/conn.php';

if(isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $valid_statuses = ['lost', 'damaged'];
    if(!in_array($status, $valid_statuses)){
        echo 'error';
        exit;
    }

    $stmt = $conn->prepare("UPDATE books SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if($stmt->execute()){
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
} else {
    echo 'error';
}
?>