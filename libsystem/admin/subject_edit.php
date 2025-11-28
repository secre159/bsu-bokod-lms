<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['edit_subject'])) {
    $id = intval($_POST['subject_id']);
    $name = trim($_POST['subject_name']);

    if(empty($name)) {
        $_SESSION['error'] = "Subject name cannot be empty.";
    } else {
        // Check for duplicate name
        $check = $conn->prepare("SELECT id FROM subject WHERE name = ? AND id != ?");
        $check->bind_param("si", $name, $id);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0) {
            $_SESSION['error'] = "Subject name already exists.";
        } else {
            $stmt = $conn->prepare("UPDATE subject SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);

            if($stmt->execute()) {
                $_SESSION['success'] = "Subject updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update subject.";
            }

            $stmt->close();
        }

        $check->close();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("location: subjects.php");
exit();
?>
