<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $conn_review = new mysqli("localhost", "root", "", "review");
    $conn_blog   = new mysqli("localhost", "root", "", "blog");

    if ($conn_review->connect_error || $conn_blog->connect_error) {
        die("Database connection failed: " . $conn_review->connect_error . " | " . $conn_blog->connect_error);
    }

    // ✅ Fetch from blog.userss using ID
    $stmt = $conn_blog->prepare("
        SELECT id, heading, author, content, img1, code, audio, video, created_at 
        FROM userss 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    $stmt->close();

    if ($blog) {
        // ✅ Insert into review.errorcode
        $stmt1 = $conn_review->prepare("
            INSERT INTO errorcode (heading, author, content, img1, code, audio, video, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt1->bind_param(
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
        $stmt1->execute();
        $stmt1->close();

        // ✅ Insert into blog.errorcode
        $stmt2 = $conn_blog->prepare("
            INSERT INTO errorcode (heading, author, content, img1, code, audio, video, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param(
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
        $stmt2->execute();
        $stmt2->close();

        // ✅ Delete from blog.userss (using ID now)
        $stmt3 = $conn_blog->prepare("DELETE FROM userss WHERE id = ?");
        $stmt3->bind_param("i", $id);
        $stmt3->execute();
        $stmt3->close();

        $_SESSION['success_message'] = "Blog moved to errorcode successfully!";
    } else {
        $_SESSION['error_message'] = "Blog not found in blog.userss!";
    }

    $conn_review->close();
    $conn_blog->close();
}

header("Location: admin_dashboard.php");
exit;
?>