<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['book_id'], $_POST['subject_id'])) {
    $book_id = intval($_POST['book_id']);
    $subject_id = intval($_POST['subject_id']);

    $sql = "INSERT IGNORE INTO book_subject_map (book_id, subject_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $book_id, $subject_id);
    $stmt->execute();

    echo "Book assigned successfully.";
} else {
    echo "Invalid data.";
}
?>
