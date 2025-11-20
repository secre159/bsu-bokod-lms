<?php
include 'includes/session.php';
include 'includes/conn.php'; // make sure your DB connection is included

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    // Fetch the specific archived student
    $stmt = $conn->prepare("SELECT * FROM archived_students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $student = $result->fetch_assoc();

        // Insert into students table
        $insert = $conn->prepare("
            INSERT INTO students 
            (student_id, firstname, middlename, lastname, email, phone, course_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "ssssssi",
            $student['student_id'],
            $student['firstname'],
            $student['middlename'],
            $student['lastname'],
            $student['email'],
            $student['phone'],
            $student['course_id']
        );
        $insert->execute();
        $insert->close();

        // Delete from archived_students
        $delete = $conn->prepare("DELETE FROM archived_students WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $delete->close();

        $_SESSION['success'] = 'Student restored successfully';
    } else {
        $_SESSION['error'] = 'Student not found';
    }
} else {
    $_SESSION['error'] = 'Invalid request';
}

// Redirect back to archived students page
header('Location: archived_student.php');
exit();
?>
