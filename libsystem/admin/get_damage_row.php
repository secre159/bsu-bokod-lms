<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $res = $conn->query("SELECT * FROM book_damage_lost WHERE id=$id");
    echo json_encode($res->fetch_assoc());
}
?>
