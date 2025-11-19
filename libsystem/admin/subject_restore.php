<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $sql = "UPDATE subjects SET status='active', archived_at=NULL WHERE id='$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Subject restored successfully';
    } else {
        $_SESSION['error'] = $conn->error;
    }
} else {
    $_SESSION['error'] = 'Select subject to restore first';
}

header('location: archived_subject.php');
?>
