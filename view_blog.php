<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db_connect.php';  // Ensure the correct DB connection

if (!isset($_GET['id'])) {
    die("No blog ID provided.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM userss WHERE id = ?"); // Ensure correct table and field
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    die("Blog not found.");
}

$editMode = isset($_GET['edit']) && $_GET['edit'] == 'true'; // Check if 'edit' is passed in URL
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Blog</title>
    <link rel="stylesheet" href="css/view_blog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ✅ Syntax Highlighting -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <style>
        .blog-content textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            line-height: 1.6;
            height: 300px;
            margin-top: 10px;
            font-family: monospace;
            white-space: pre-wrap; /* Preserve line breaks */
        }

        .comment-btn {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .comment-btn:hover {
            background-color: #138496;
        }

        /* ✅ Media Alignment + Size Control */
        .blog-media {
            display: block;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
        }

        img.blog-media {
            max-width: 70%;   /* reduce image size */
            max-height: 350px;
            object-fit: contain;
        }

        video.blog-media {
            max-width: 70%;   /* reduce video size */
            max-height: 320px;
        }

        audio.blog-media {
            max-width: 70%;   /* reduce audio size */
        }

        .code-block {
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            border-radius: 8px;
        }

        .actions {
            margin-top: 20px;
            text-align: center;
        }

        .actions form, .actions button {
            margin: 5px;
        }
    </style>
</head>

<body>
<div class="header">
    <h1><?= htmlspecialchars($blog['heading']) ?></h1>
    <p><em>By <?= htmlspecialchars($blog['author']) ?></em></p>
    <a href="admin_dashboard.php" class="back-button">← Back</a>
</div>

<div class="blog-container">
    <p><strong>Submitted At:</strong> <?= htmlspecialchars($blog['created_at']) ?></p>

    <!-- ✅ Blog Content -->
    <div class="blog-content">
        <?php if (!$editMode) : ?>
            <!-- Display normal blog content -->
            <div class="blog-text">
                <?php 
                    $fixedContent = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $blog['content']);
                    $parts = preg_split('/```/', $fixedContent);
                    $isCode = false;

                    foreach ($parts as $part) {
                        if ($isCode) {
                            echo '<pre><code class="language-php">' . htmlspecialchars(trim($part)) . '</code></pre>';
                        } else {
                            echo '<div class="blog-text">' . nl2br(strip_tags(trim($part), '<p><b><i><u><br><strong><em>')) . '</div>';
                        }
                        $isCode = !$isCode;
                    }
                ?>
            </div>
        <?php else: ?>
            <!-- Show textarea for editing -->
            <form method="POST" action="save_blog.php">
                <textarea name="content"><?= htmlspecialchars($blog['content']) ?></textarea>
                <input type="hidden" name="id" value="<?= $blog['id'] ?>">
                <button type="submit" class="save-btn">Save</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- ✅ Show Image -->
    <?php 
    if (!empty($blog['img1'])) {
        $imgPath = str_replace('\\', '/', $blog['img1']);
        if (file_exists($imgPath)) {
            echo '<img src="' . htmlspecialchars($imgPath) . '" alt="Blog Image" class="blog-media">';
        } else {
            echo '<img src="default.jpg" alt="Default Image" class="blog-media">';
        }
    } else {
        echo '<img src="default.jpg" alt="Default Image" class="blog-media">';
    }
    ?>

    <!-- ✅ Show Video -->
    <?php 
    if (!empty($blog['video'])) {
        $videoPath = str_replace('\\', '/', $blog['video']); 
        echo '<video src="' . htmlspecialchars($videoPath) . '" controls class="blog-media"></video>';
    }
    ?>

    <!-- ✅ Show Audio -->
    <?php 
    if (!empty($blog['audio'])) {
        $audioPath = str_replace('\\', '/', $blog['audio']);
        echo '<audio src="' . htmlspecialchars($audioPath) . '" controls class="blog-media"></audio>';
    }
    ?>

    <!-- ✅ Show Uploaded Code File -->
    <?php if (!empty($blog['code']) && file_exists(str_replace('\\', '/', $blog['code']))): ?>
        <div class="code-block">
            <h3>Submitted Code:</h3>
            <?php
            $fileContent = file_get_contents(str_replace('\\', '/', $blog['code']));
            $fixedFileContent = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $fileContent);
            ?>
            <pre><code class="language-php"><?= htmlspecialchars($fixedFileContent) ?></code></pre>
        </div>
    <?php endif; ?>
</div>

<!-- ✅ Action Buttons -->
<div class="actions">
    <form method="POST" action="approve_blog.php" style="display:inline;">
        <input type="hidden" name="id" value="<?= $blog['id'] ?>">
        <button class="approve-btn"><i class="fas fa-check"></i> Approve</button>
    </form>

    <form method="POST" action="reject_blog.php" style="display:inline;">
        <input type="hidden" name="id" value="<?= $blog['id'] ?>">
        <button class="reject-btn"><i class="fas fa-times"></i> Reject</button>
    </form>

    <form method="POST" action="recorrect_blog.php" style="display:inline;">
        <input type="hidden" name="id" value="<?= $blog['id'] ?>">
        <button class="block-btn"><i class="fas fa-ban"></i> Re-Correct</button>
    </form>

    <button type="button" class="comment-btn" id="commentBtn"><i class="fas fa-comment"></i> Comment</button>
</div>

<!-- ✅ Hidden Comment Box -->
<div id="commentBox" style="display:none; margin-top:15px;" class="comment-box">
    <form method="POST" action="submit_comment.php">
        <textarea name="comment" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;" placeholder="Write your comment..."></textarea>
        <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
        <button type="submit" class="submit-comment-btn">Submit Comment</button>
    </form>
</div>

<!-- ✅ Display Comments -->
<div class="comments-section" style="margin-top:30px;">
    <h3>Comments</h3>
    <?php
    $stmt = $conn->prepare("SELECT comment, created_at FROM comments WHERE blog_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $comments = $stmt->get_result();

    if ($comments->num_rows > 0) {
        while ($row = $comments->fetch_assoc()) {
            echo "<div class='comment-box' style='border:1px solid #ddd; padding:10px; border-radius:8px; margin-bottom:10px;'>";
            echo "<strong>Admin</strong> <small>(" . $row['created_at'] . ")</small><br>";
            echo nl2br(htmlspecialchars(strip_tags($row['comment'])));
            echo "</div>";
        }
    } else {
        echo "<p>No comments yet.</p>";
    }
    ?>
</div>

<script>
document.getElementById("commentBtn").addEventListener("click", function() {
    var box = document.getElementById("commentBox");
    box.style.display = (box.style.display === "none") ? "block" : "none";
});
</script>

</body>
</html>
