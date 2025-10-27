<?php
include 'db_connect.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Create folders if not exist
    $folders = ['uploads/images', 'uploads/audio', 'uploads/video', 'uploads/code'];
    foreach ($folders as $folder) {
        if (!is_dir($folder)) mkdir($folder, 0777, true);
    }

    // Upload files
    $imagePath = '';
    $codePath = '';
    $audioPath = '';
    $videoPath = '';

    if ($_FILES['image']['name']) {
        $imagePath = "uploads/images/" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    if ($_FILES['code']['name']) {
        $codePath = "uploads/code/" . basename($_FILES['code']['name']);
        move_uploaded_file($_FILES['code']['tmp_name'], $codePath);
    }

    if ($_FILES['audio']['name']) {
        $audioPath = "uploads/audio/" . basename($_FILES['audio']['name']);
        move_uploaded_file($_FILES['audio']['tmp_name'], $audioPath);
    }

    if ($_FILES['video']['name']) {
        $videoPath = "uploads/video/" . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $videoPath);
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO blog_submissions (email, title, content, image_path, code_file, audio_file, video_file) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $email, $title, $content, $imagePath, $codePath, $audioPath, $videoPath);

    if ($stmt->execute()) {
        echo "Blog submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
