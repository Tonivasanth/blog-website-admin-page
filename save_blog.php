<?php
// Ensure admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $content = htmlspecialchars($_POST['content']); // Make sure content is safe

    // Update blog content in the database
    $stmt = $conn->prepare("UPDATE users SET content = ? WHERE id = ?");
    $stmt->bind_param("si", $content, $id);
    if ($stmt->execute()) {
        // Redirect to the blog page or show success message
        header("Location: view_blog.php?id=$id"); // Redirect back to the same page
        exit;
    } else {
        echo "Error updating content.";
    }
}
?>
