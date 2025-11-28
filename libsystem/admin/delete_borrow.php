<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Only delete if not returned
    $stmt = $conn->prepare("DELETE FROM borrow_transactions WHERE id = ? AND status != 'returned'");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Borrow record deleted successfully.";
    } else {
        $_SESSION['error'] = "Unable to delete borrow record.";
    }

    header("Location: reports.php"); // change if needed
    exit();
}
?>
