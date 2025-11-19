<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    // Fetch faculty record before deleting
    $check = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $faculty = $result->fetch_assoc();

        // Insert into archived_faculty
        $archive = $conn->prepare("
            INSERT INTO archived_faculty (id, firstname, lastname, email, position, photo)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $archive->bind_param(
            "isssss",
            $faculty['id'],
            $faculty['firstname'],
            $faculty['lastname'],
            $faculty['email'],
            $faculty['position'],
            $faculty['photo']
        );

        if ($archive->execute()) {

            // Delete from faculty table
            $stmt = $conn->prepare("DELETE FROM faculty WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Faculty member archived successfully.';
            } else {
                $_SESSION['error'] = 'Error deleting faculty member after archiving.';
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = 'Error archiving faculty member.';
        }

        $archive->close();
    } else {
        $_SESSION['error'] = 'Faculty record not found.';
    }

    $check->close();
} else {
    $_SESSION['error'] = 'Select a faculty member to delete first.';
}

header('location: faculty.php');
exit;
?>
