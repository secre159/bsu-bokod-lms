<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admin WHERE gmail = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        $_SESSION['error'] = "No account found with that email.";
    } else {
        $row = $result->fetch_assoc();

        // ✅ Verify hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['id']; // store admin ID in session
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Please input admin credentials first.";
}

// Redirect back to login page
header("Location: index.php");
exit();
?>
