<?php
session_start();
include 'db_connect.php';

$login_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $domain_id = $_POST['domain_id'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admindetails WHERE domain_id = ? AND role = 'Admin'");
    $stmt->bind_param("s", $domain_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $domain_id;
            $_SESSION['LAST_ACTIVITY'] = time(); // initialize session timer
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $login_msg = "❌ Invalid password.";
        }
    } else {
        $login_msg = "❌ Admin not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AAC</title>
  <link rel="stylesheet" href="adminlogin.css">
</head>
<body>

<div class="background"></div>

<div class="header">
  <div class="header-logo">
    <img src="abc.png" alt="Logo">
  </div>
  <div class="header-title">AAC Admin Login</div>
</div>

<div class="login-container">
  <h2 class="login-heading">Admin Login</h2>

  <?php if (isset($_GET['msg'])): ?>
    <p style="color: <?= $_GET['msg'] === 'signup_success' ? 'green' : 'red' ?>;">
      <?= $_GET['msg'] === 'signup_success' ? '✅ Signup successful! Please login.' :
           ($_GET['msg'] === 'session_expired' ? '⚠️ Session expired. Please login again.' :
           ($_GET['msg'] === 'logged_out' ? '✅ Logged out successfully.' : '')) ?>
    </p>
  <?php endif; ?>

  <form method="post" class="login-form">
    <input type="email" name="domain_id" placeholder="Email ID" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" class="login-button">Login</button>
  </form>

  <div class="signup-text">
    Don't have an account? <a class="signup-link" href="admin_signup.php">Signup</a>
  </div>

  <?php if ($login_msg): ?>
    <p style="color:red; margin-top:10px;"><?= $login_msg ?></p>
  <?php endif; ?>
</div>

</body>
</html>
