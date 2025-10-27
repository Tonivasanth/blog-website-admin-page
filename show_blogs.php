<?php
// 1. Include database connection
include 'db_connect.php';

// 2. Fetch all approved blogs
$result = $conn->query("SELECT * FROM approved_blogs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Blog Viewer</title>
    <link rel="stylesheet" href="show_blogs.css">
    
    <!-- Highlight.js for code formatting -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
    
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9ff;
            padding: 20px;
            color: #222;
        }
        h2 { color: #0047a3; }
        h3 { margin-top: 30px; color: #2a2a2a; }
        .blog-content { font-size: 16px; line-height: 1.6; margin: 10px 0; }
        .blog-media { margin: 10px 0; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .download-link { display: inline-block; margin-top: 10px; color: #007bff; text-decoration: none; font-weight: bold; }
        .download-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>Published Blogs</h2>
<a href="show_blogs.php">View All Approved Blogs</a>

<?php while ($blog = $result->fetch_assoc()): ?>
    <hr>

    <!-- Blog title -->
    <h3><?= htmlspecialchars($blog['heading']) ?></h3>

    <!-- Author and posted date -->
    <p><strong>Author:</strong> <?= htmlspecialchars($blog['author']) ?></p>
    <p><strong>Posted at:</strong> <?= htmlspecialchars($blog['created_at']) ?></p>

    <!-- Blog content -->
    <div class="blog-content">
        <?php
        // Fix escaped line breaks and handle code blocks
        $content = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $blog['content']);
        $parts = preg_split('/```/', $content); // Split code blocks
        $isCode = false;

        foreach ($parts as $part) {
            if ($isCode) {
                echo '<pre><code class="language-php">' . htmlspecialchars(trim($part)) . '</code></pre>';
            } else {
                echo '<div>' . nl2br(strip_tags(trim($part), '<p><b><i><u><br><strong><em>')) . '</div>';
            }
            $isCode = !$isCode;
        }
        ?>
    </div>

    <!-- Image -->
    <?php
    if (!empty($blog['img1'])) {
        $imgPath = "uploads/image/" . basename($blog['img1']);
        if (file_exists($imgPath)) {
            echo '<img src="' . htmlspecialchars($imgPath) . '" width="300" class="blog-media"><br>';
        }
    }
    ?>

    <!-- Video -->
    <?php
    if (!empty($blog['video'])) {
        $videoPath = "uploads/video/" . basename($blog['video']);
        if (file_exists($videoPath)) {
            echo '<video width="400" controls class="blog-media"><source src="' . htmlspecialchars($videoPath) . '">Your browser does not support video.</video><br>';
        }
    }
    ?>

    <!-- Audio -->
    <?php
    if (!empty($blog['audio'])) {
        $audioPath = "uploads/audio/" . basename($blog['audio']);
        if (file_exists($audioPath)) {
            echo '<audio controls class="blog-media"><source src="' . htmlspecialchars($audioPath) . '">Your browser does not support audio.</audio><br>';
        }
    }
    ?>

    <!-- Code file download -->
    <?php
    if (!empty($blog['code'])) {
        $codePath = "uploads/code/" . basename($blog['code']);
        if (file_exists($codePath)) {
            echo '<a href="' . htmlspecialchars($codePath) . '" download class="download-link">Download Code</a><br>';
        }
    }
    ?>

<?php endwhile; ?>

</body>
</html>
