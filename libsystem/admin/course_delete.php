<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid course selected.';
        header('Location: student.php');
        exit();
    }

    // Check if course exists
    $check = $conn->prepare("SELECT * FROM course WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Course not found.';
        $check->close();
        header('Location: student.php');
        exit();
    }
    $check->close();

    // Delete the course
    $stmt = $conn->prepare("DELETE FROM course WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Course deleted successfully.';
    } else {
        $_SESSION['error'] = 'Error deleting course: ' . $conn->error;
    }

    $stmt->close();
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

header('Location: student.php');
exit();
?>
