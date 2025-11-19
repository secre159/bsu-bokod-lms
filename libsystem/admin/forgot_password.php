<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();
include '../includes/conn.php';

require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    // Check if email exists in the admin table
    $stmt = $conn->prepare("SELECT * FROM admin WHERE gmail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        date_default_timezone_set('Asia/Manila');
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // ✅ Save reset token directly in admin table
        $stmt_update = $conn->prepare("UPDATE admin SET reset_token = ?, reset_expires = ? WHERE gmail = ?");
        $stmt_update->bind_param("sss", $token, $expiry, $email);
        $stmt_update->execute();

        // Send reset email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'marijoysapditbsu@gmail.com'; // your Gmail
            $mail->Password   = 'ihzfufsmsyobxxaf';            // your 16-character app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('marijoysapditbsu@gmail.com', 'BSU Library System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $resetLink = "http://localhost/Libsystem4/libsystem/admin/reset_password.php?token=$token";
            $mail->Body = "
                <h2>BSU Library Management System</h2>
                <p>You requested a password reset. Click the link below to set a new password:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
            $_SESSION['success'] = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }

    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    background-color: #0b3d2e;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }
  .card {
    width: 400px;
    border-radius: 12px;
    border: 2px solid #d4af37;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  }
  .btn-gold {
    background-color: #d4af37;
    color: #0b3d2e;
    font-weight: bold;
  }
  .btn-gold:hover {
    background-color: #e6c75b;
  }
</style>
</head>
<body>
  <div class="card p-4">
    <h3 class="text-center mb-3 text-success">Forgot Password</h3>
    <p class="text-muted text-center">Enter your email to receive a password reset link.</p>

    <?php
      if(isset($_SESSION['error'])){
        echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
        unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
        echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
        unset($_SESSION['success']);
      }
    ?>

    <form method="POST">
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <button type="submit" class="btn btn-gold w-100">Send Reset Link</button>
    </form>

    <div class="text-center mt-3">
      <a href="index.php" class="text-light">← Back to Login</a>
    </div>
  </div>
</body>
</html>
