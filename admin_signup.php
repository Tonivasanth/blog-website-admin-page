<?php
include "db_connect.php";
$signup_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $domain_id = htmlspecialchars(trim($_POST['domain_id']));
    $role = htmlspecialchars(trim($_POST['role']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $signup_msg = "❌ Passwords do not match!";
    } else {
        $check = $conn->prepare("SELECT domain_id FROM admindetails WHERE domain_id = ?");
        $check->bind_param("s", $domain_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $signup_msg = "❌ Domain ID already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admindetails (domain_id, role, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $domain_id, $role, $hashed_password);

            if ($stmt->execute()) {
                header("Location: admin_login.php?msg=signup_success");
                exit();
            } else {
                $signup_msg = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Signup</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="auth-container">
        <h2 class="auth-heading">Admin Signup</h2>

        <form method="post" autocomplete="off" class="auth-form">
            <input type="email" name="domain_id" placeholder="Email ID" required>
            
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="Admin">Admin</option>
            </select>
            
            <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
            <input type="password" name="confirm" placeholder="Confirm Password" required autocomplete="new-password">
            
            <button type="submit" class="auth-button">Signup</button>
        </form>

        <?php if ($signup_msg): ?>
            <p class="auth-message <?= strpos($signup_msg, '❌') === false ? 'success' : 'error' ?>">
                <?= $signup_msg ?>
            </p>
        <?php endif; ?>

        <p class="login-link">Already have an account? <a href="admin_login.php">Login</a></p>
    </div>
</body>
</html>
