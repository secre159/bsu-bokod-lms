<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    // Optional: Check if the transaction exists
    $check = $conn->query("SELECT * FROM borrow_transactions WHERE id = $id");
    if($check->num_rows > 0){
        // Delete the record
        if($conn->query("DELETE FROM borrow_transactions WHERE id = $id")){
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Record not found";
    }
} else {
    echo "Invalid request";
}
?>
