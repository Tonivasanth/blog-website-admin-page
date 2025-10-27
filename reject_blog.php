<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM userss WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Blog rejected and deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete blog. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Prepare failed: " . $conn->error;
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
}

header("Location: admin_dashboard.php");
exit;
?>
