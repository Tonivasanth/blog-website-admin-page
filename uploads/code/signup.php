<?php
include "db.php";
$signup_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $roll_no = $_POST['roll_no'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $signup_msg = "❌ Passwords do not match!";
    } else {
        $stmt = $conn->prepare("INSERT INTO info (email, roll_no, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $roll_no, $role, $password);

        if ($stmt->execute()) {
            $signup_msg = "✅ Signup successful!";
        } else {
            $signup_msg = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup</title>
  <link rel="stylesheet" href="signup.css" />
</head>
<body>
  <div class="signup-container">
    <h2>Signup</h2>
    <form method="post" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="text" name="roll_no" placeholder="Roll Number" required />
      <select name="role" required>
        <option value="">Select</option>
        <option value="student">Student</option>
        <option value="faculty">Faculty</option>
      </select>
      <input type="password" name="password" placeholder="Create your password" required />
      <input type="password" name="confirm" placeholder="Confirm your password" required />
      <button type="submit">Signup</button>
    </form>
    <p class="login-link">Already have an account? <a href="login.php">Login</a></p>

    <?php if ($signup_msg): ?>
      <p style="color:red;"><?php echo $signup_msg; ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
