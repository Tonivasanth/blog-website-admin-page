<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db_review.php'; // database connection to "review" DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    // Delete from blog_submissions table
    $sql = "DELETE FROM blog_submissions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Blog deleted successfully");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
