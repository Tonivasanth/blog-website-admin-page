<!DOCTYPE html>
<html>
<head>
    <title>Submit Blog</title>
</head>
<body>
    <h2>Submit Your Blog</h2>
    <form action="publish_blog.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="email" placeholder="Your Email" required><br><br>
        <input type="text" name="title" placeholder="Blog Title" required><br><br>
        <textarea name="content" rows="5" placeholder="Blog Content" required></textarea><br><br>

        <label>Upload Image:</label>
        <input type="file" name="img1" accept="image/*"><br><br>

        <label>Upload Code File (.txt, .py, .cpp, .java, etc.):</label>
        <input type="file" name="code" accept=".py,.txt,.js,.html,.cpp,.java"><br><br>

        <label>Upload Audio:</label>
        <input type="file" name="audio" accept="audio/*"><br><br>

        <label>Upload Video:</label>
        <input type="file" name="video" accept="video/*"><br><br>

        <!-- Optional user ID if needed -->
        <!-- <input type="hidden" name="user_id" value="your_user_id_here"> -->

        <button type="submit" name="submit">Submit Blog</button>
    </form>
</body>
</html>
