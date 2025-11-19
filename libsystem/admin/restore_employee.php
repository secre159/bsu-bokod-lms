<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    $query = $conn->prepare("SELECT * FROM archived_faculty WHERE id=?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $employee = $result->fetch_assoc();

    if($employee){
        $insert = $conn->prepare("INSERT INTO faculty (firstname, lastname, email, position) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $employee['firstname'], $employee['lastname'], $employee['email'], $employee['position']);
        $insert->execute();

        $delete = $conn->prepare("DELETE FROM archived_faculty WHERE id=?");
        $delete->bind_param("i", $id);
        $delete->execute();

        $_SESSION['success'] = 'Employee restored successfully.';
    } else {
        $_SESSION['error'] = 'Employee not found.';
    }
} else {
    $_SESSION['error'] = 'Invalid request.';
}

header('location: archived_employee.php');
exit;
?>
