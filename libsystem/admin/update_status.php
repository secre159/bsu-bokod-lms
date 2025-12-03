<?php
include 'includes/conn.php'; // Make sure this path is correct

// Read the JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    echo 'fail'; // Invalid input
    exit;
}

$bookId = intval($input['id']);
$status = 'available';

// Prepare and execute the SQL statement
$stmt = $conn->prepare("UPDATE books SET status = ? WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("si", $status, $bookId);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'fail';
    }
    $stmt->close();
} else {
    echo 'fail';
}

$conn->close();
?>