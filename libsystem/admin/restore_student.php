<?php
include 'includes/session.php';

// Fetch all archived students
$query = $conn->query("SELECT * FROM archived_students");

if($query->num_rows > 0){
    while($student = $query->fetch_assoc()){
        // Insert into students table including student_id
        $stmt = $conn->prepare("INSERT INTO students (student_id, firstname, lastname, course_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param(
            "sssi",
            $student['student_id'],   // original student ID
            $student['firstname'],
            $student['lastname'],
            $student['course_id']
        );
        $stmt->execute();
        $stmt->close();

        // Delete from archived_students
        $stmt = $conn->prepare("DELETE FROM archived_students WHERE id = ?");
        $stmt->bind_param("i", $student['id']);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['success'] = 'All archived students restored successfully';
} else {
    $_SESSION['error'] = 'No archived students found';
}

// Redirect back to archived students page
header('Location: archived_student.php');
exit();
?>
