<?php
include 'includes/session.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/conn.php';
include 'includes/scripts.php';

// Handle form submission
if(isset($_POST['submit_suggestion'])) {
    $user_id = $_SESSION['student'] ?? $_SESSION['teacher'] ?? '';
    $user_type = isset($_SESSION['student']) ? 'Student' : 'Teacher';
    $book_title = $conn->real_escape_string($_POST['book_title']);
    $author = $conn->real_escape_string($_POST['author']);
    $reason = $conn->real_escape_string($_POST['reason']);

    $sql = "INSERT INTO book_suggestions (user_id, user_type, book_title, author, reason)
            VALUES ('$user_id', '$user_type', '$book_title', '$author', '$reason')";
    if($conn->query($sql)){
        $_SESSION['success'] = "Your book suggestion has been submitted successfully!";
    } else {
        $_SESSION['error'] = "Failed to submit suggestion. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Suggest a Book</title>
  <style>
    .suggest-panel {
        max-width: 600px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #198754;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .suggest-panel h3 { text-align: center; color: #004d00; margin-bottom: 20px; }
    .suggest-panel .form-control { margin-bottom: 12px; }
    .suggest-panel button { background: #004d00; color: #FFD700; }
  </style>
</head>
<body>
<main class="container">

<div class="suggest-panel">
    <h3>Suggest a Book</h3>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" class="form-control" name="book_title" placeholder="Book Title" required>
        <input type="text" class="form-control" name="author" placeholder="Author">
        <textarea class="form-control" name="reason" placeholder="Why do you suggest this book?" rows="4"></textarea>
        <button type="submit" name="submit_suggestion" class="btn w-100">Submit Suggestion</button>
    </form>
</div>

</main>
</body>
</html>
