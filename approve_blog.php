<?php
session_start();
include 'db_connect.php'; // this connects to review DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = intval($_POST['id']);

    // ✅ Fetch blog details from review.userss (pending table)
    $stmt = $conn->prepare("SELECT * FROM userss WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    $stmt->close();

    if ($blog) {
        // ✅ Insert into review.approved_blogs
        $insert_review = $conn->prepare("
            INSERT INTO approved_blogs 
            (heading, author, content, img1, code, audio, video, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert_review->bind_param(
            "ssssssss",
            $blog['heading'],
            $blog['author'],
            $blog['content'],
            $blog['img1'],
            $blog['code'],
            $blog['audio'],
            $blog['video'],
            $blog['created_at']
        );

        if ($insert_review->execute()) {
            // ✅ Connect to BLOG database for copy
            $conn_blog = new mysqli("localhost", "root", "", "blog");
            if ($conn_blog->connect_error) {
                die("Blog DB connection failed: " . $conn_blog->connect_error);
            }

            $insert_blog = $conn_blog->prepare("
                INSERT INTO approved_blogs 
                (heading, author, content, img1, code, audio, video, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert_blog->bind_param(
                "ssssssss",
                $blog['heading'],
                $blog['author'],
                $blog['content'],
                $blog['img1'],
                $blog['code'],
                $blog['audio'],
                $blog['video'],
                $blog['created_at']
            );
            $insert_blog->execute();
            $insert_blog->close();
            $conn_blog->close();

            // ✅ Delete from review.userss (pending table)
            $del = $conn->prepare("DELETE FROM userss WHERE id = ?");
            $del->bind_param("i", $blog_id);
            $del->execute();
            $del->close();

            $_SESSION['success_message'] = "Blog '<b>" . htmlspecialchars($blog['heading']) . "</b>' approved successfully (stored in review & copied to blog)!";
        } else {
            $_SESSION['error_message'] = "Error approving blog: " . $insert_review->error;
        }

        $insert_review->close();
    } else {
        $_SESSION['error_message'] = "Blog not found.";
    }

    header("Location: admin_dashboard.php");
    exit;
}
?>
