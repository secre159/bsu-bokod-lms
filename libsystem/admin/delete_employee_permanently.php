<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = intval($_POST['id']);
    $delete = $conn->prepare("DELETE FROM archived_faculty WHERE id=?");
    $delete->bind_param("i", $id);
    $delete->execute();

    $_SESSION['success'] = 'Employee deleted permanently.';
} else {
    $_SESSION['error'] = 'Invalid request.';
}

header('location: archived_employee.php');
exit;
?>
