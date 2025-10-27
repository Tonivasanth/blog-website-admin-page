<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Fetch blog and insert into approved_blogs
        $stmt = $conn->prepare("SELECT * FROM blog_submissions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $stmt2 = $conn->prepare("INSERT INTO approved_blogs (title, content, submitted_at) VALUES (?, ?, ?)");
            $stmt2->bind_param("sss", $row['title'], $row['content'], $row['submitted_at']);
            $stmt2->execute();
        }

        // Remove from submissions
        $del = $conn->prepare("DELETE FROM blog_submissions WHERE id = ?");
        $del->bind_param("i", $id);
        $del->execute();
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE blog_submissions SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($action === 'block') {
        $stmt = $conn->prepare("UPDATE blog_submissions SET status = 'blocked' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

header("Location: admin_dashboard.php");
exit;
