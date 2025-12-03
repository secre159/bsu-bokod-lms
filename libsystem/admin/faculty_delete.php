<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("UPDATE faculty SET archived = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        $_SESSION['success'] = "Faculty archived successfully.";
    } else {
        $_SESSION['error'] = "Error archiving faculty.";
    }
} else {
    $_SESSION['error'] = "No faculty selected.";
}

header("Location: faculty.php");
exit;
?>
