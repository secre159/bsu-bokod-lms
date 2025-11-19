<?php
include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Fetch student details first
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student) {
        // Extract details
        $student_id = $student['student_id'];
        $firstname  = $student['firstname'];
        $lastname   = $student['lastname'];
        $email      = $student['email'];
        $phone      = $student['phone'];
        $course_id  = $student['course_id'];

        // Archive record
        $stmt2 = $conn->prepare("INSERT INTO archived_students 
            (student_id, firstname, lastname, email, phone, course_id, archived_on) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt2->bind_param("sssssi", $student_id, $firstname, $lastname, $email, $phone, $course_id);

        if ($stmt2->execute()) {
            $stmt2->close();

            // Delete original record
            $stmt3 = $conn->prepare("DELETE FROM students WHERE id = ?");
            $stmt3->bind_param("i", $id);
            if ($stmt3->execute()) {
                $_SESSION['success'] = 'Student archived and removed successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete student: ' . $conn->error;
            }
            $stmt3->close();
        } else {
            $_SESSION['error'] = 'Failed to archive student: ' . $conn->error;
        }
    } else {
        $_SESSION['error'] = 'Student not found';
    }
} else {
    $_SESSION['error'] = 'Select student to delete first';
}

// Redirect to the archived student list
header('Location: archived_student.php');
exit();
?>
