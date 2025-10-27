<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db_connect.php'; // Ensure the correct DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $summary = $_POST['summary'];
    $content = $_POST['content'];

    // Handle the image upload
    if (isset($_FILES['img1']) && $_FILES['img1']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['img1']['tmp_name'];
        $imageName = $_FILES['img1']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid() . '.' . $imageExtension; // Ensure unique image names

        // Move the uploaded image to the 'uploads' directory
        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            die("Failed to upload image.");
        }
    } else {
        $imagePath = null; // If no image is uploaded, set to null
    }

    // Insert blog post into the 'users' table in the 'blog' database
    $stmt = $conn->prepare("INSERT INTO userss (title, author, summary, content, img1, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $title, $author, $summary, $content, $imagePath);
    $stmt->execute();

    // Redirect to success page or show success message
    $_SESSION['success_message'] = "Blog published successfully!";
    header("Location: admin_dashboard.php");
    exit;
}
?>
