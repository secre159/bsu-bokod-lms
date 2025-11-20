<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['approve'])){

    $id = $_POST['id'];

    // Get suggested book data
    $sql = "SELECT * FROM suggested_books WHERE id = '$id'";
    $query = $conn->query($sql);

    if($query->num_rows > 0){
        $row = $query->fetch_assoc();

        $title = mysqli_real_escape_string($conn, $row['title']);
        $author = mysqli_real_escape_string($conn, $row['author']);
        $isbn = mysqli_real_escape_string($conn, $row['isbn']);
        $subject = mysqli_real_escape_string($conn, $row['subject']);
        $description = mysqli_real_escape_string($conn, $row['description']);

        // Insert into physical books table
        $insert = "INSERT INTO books (title, author, isbn, subject, description, date_added, status) 
                   VALUES ('$title', '$author', '$isbn', '$subject', '$description', NOW(), 'Available')";

        if($conn->query($insert)){
            // Instead of deleting, update status to 'Approved'
            $conn->query("UPDATE suggested_books SET status='Approved' WHERE id = '$id'");
            $_SESSION['success'] = "Suggested book has been approved and added to the library!";
        }
        else{
            $_SESSION['error'] = "Failed to approve suggested book: " . $conn->error;
        }
    }
    else{
        $_SESSION['error'] = "Book suggestion not found.";
    }

} 
else {
    $_SESSION['error'] = "Invalid request.";
}

header('location: suggested_books.php');
exit;
?>
