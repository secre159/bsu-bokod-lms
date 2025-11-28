<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['add_subject'])) {
    $name = trim($_POST['subject_name']);

    if (empty($name)) {
        $_SESSION['error'] = "Subject name cannot be empty.";
    } else {
        // ✅ Check if subject already exists (case insensitive)
        $check_stmt = $conn->prepare("SELECT id FROM subject WHERE LOWER(name) = LOWER(?)");
        $check_stmt->bind_param("s", $name);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $_SESSION['error'] = "Subject already exists.";
        } else {
            // ✅ Safe insert if not existing
            $stmt = $conn->prepare("INSERT INTO subject (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "New subject added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add subject.";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header('location: subjects.php');
exit();
?>
