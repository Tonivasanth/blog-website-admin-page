<?php
// Connect to blog database
$blog_conn = new mysqli("localhost", "root", "", "blog");
if ($blog_conn->connect_error) {
    die("Connection failed (blog DB): " . $blog_conn->connect_error);
}

// Connect to review database
$review_conn = new mysqli("localhost", "root", "", "review");
if ($review_conn->connect_error) {
    die("Connection failed (review DB): " . $review_conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = mysqli_real_escape_string($blog_conn, $_POST['comment']); // escape using one conn
    $blog_id = intval($_POST['blog_id']);
    $created_at = date("Y-m-d H:i:s");

    // Insert into blog.comment
    $sql_blog = "INSERT INTO comments (blog_id, comment, created_at) VALUES ('$blog_id', '$comment', '$created_at')";
    $blog_result = mysqli_query($blog_conn, $sql_blog);

    // Insert into review.comment
    $sql_review = "INSERT INTO comments (blog_id, comment, created_at) VALUES ('$blog_id', '$comment', '$created_at')";
    $review_result = mysqli_query($review_conn, $sql_review);

    if ($blog_result && $review_result) {
        header("Location: view_blog.php?id=" . $blog_id);
        exit;
    } else {
        echo "Error (blog DB): " . mysqli_error($blog_conn) . "<br>";
        echo "Error (review DB): " . mysqli_error($review_conn);
    }
}

// Close connections
$blog_conn->close();
$review_conn->close();
?>
