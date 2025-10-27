<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db_connect.php'; // connects to review DB

if (!isset($_GET['id'])) {
    die("No blog ID provided.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM review.recorrection WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();
$stmt->close();

if (!$blog) {
    die("Blog not found.");
}

$editMode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Handle Approve
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $approve_id = intval($_POST['approve_id']);

    $stmtBlog = $conn->prepare("SELECT * FROM recorrection WHERE id = ?");
    $stmtBlog->bind_param("i", $approve_id);
    $stmtBlog->execute();
    $resultBlog = $stmtBlog->get_result();
    $blogData = $resultBlog->fetch_assoc();
    $stmtBlog->close();

    if ($blogData) {
        // Insert into review.approved_blog
        $insertReview = $conn->prepare("INSERT INTO approved_blogs (heading, author, content, img1, code, audio, video, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertReview->bind_param("ssssssss", $blogData['heading'], $blogData['author'], $blogData['content'], $blogData['img1'], $blogData['code'], $blogData['audio'], $blogData['video'], $blogData['created_at']);
        $insertReview->execute();
        $insertReview->close();

        // Insert into blog.approved_blog
        $connBlog = new mysqli("localhost", "root", "", "blog");
        if (!$connBlog->connect_error) {
            $insertBlog = $connBlog->prepare("INSERT INTO approved_blogs (heading, author, content, img1, code, audio, video, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insertBlog->bind_param("ssssssss", $blogData['heading'], $blogData['author'], $blogData['content'], $blogData['img1'], $blogData['code'], $blogData['audio'], $blogData['video'], $blogData['created_at']);
            $insertBlog->execute();
            $insertBlog->close();
            $connBlog->close();
        }

        // Delete from recorrection
        $stmtDel = $conn->prepare("DELETE FROM recorrection WHERE id = ?");
        $stmtDel->bind_param("i", $approve_id);
        $stmtDel->execute();
        $stmtDel->close();

        $_SESSION['success_message'] = "Blog approved successfully!";
        header("Location: admin_recheck.php");
        exit;
    }
}

// Handle Reject via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id'])) {
    header('Content-Type: application/json');
    $rejectId = intval($_POST['reject_id']);
    $stmtDel = $conn->prepare("DELETE FROM recorrection WHERE id = ?");
    if ($stmtDel) {
        $stmtDel->bind_param("i", $rejectId);
        if ($stmtDel->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not delete blog']);
        }
        $stmtDel->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>View Blog</title>
<link rel="stylesheet" href="css/view_blog.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>
<style>
.blog-content textarea { width:100%; padding:10px; font-family:monospace; white-space:pre-wrap; height:300px; margin-top:10px; }
.blog-media { max-width:100%; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.2); }
pre code { display:block; padding:10px; background:#f0f0f0; border-radius:6px; overflow-x:auto; font-family:Consolas, monospace; }
.actions button { margin-right:10px; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; background:#4e54c8; color:white; }
.actions button:hover { background:#3b3fc0; }
.save-btn { background:#28a745; }
.save-btn:hover { background:#218838; }

/* ✅ Media vertical alignment (Image → Audio → Video) */
.media-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 15px;
    align-items: center; /* center items */
}
.media-container img {
    width: 60%;   /* reduced image width */
    height: auto;
}
.media-container video {
    width: 60%;   /* reduced video width */
    height: auto;
}
.media-container audio {
    width: 80%;   /* audio a bit wider */
}
</style>
</head>
<body>

<div class="header">
<h1><?= htmlspecialchars($blog['heading']) ?></h1>
<p><em>By <?= htmlspecialchars($blog['author']) ?></em></p>
<a href="admin_recheck.php">← Back</a>
</div>

<div class="blog-container" id="blog-<?= $blog['id'] ?>">
<p><strong>Submitted At:</strong> <?= htmlspecialchars($blog['created_at']) ?></p>

<div class="blog-content">
<?php if (!$editMode): ?>
<?php
$fixedContent = str_replace(["\\r\\n","\\r","\\n"], "\n", $blog['content']);
$parts = preg_split('/```/', $fixedContent);
$isCode = false;
foreach ($parts as $part) {
    if ($isCode) echo '<pre><code class="language-php">'.htmlspecialchars(trim($part)).'</code></pre>';
    else echo '<div>'.nl2br(strip_tags(trim($part), '<p><b><i><u><br><strong><em>')).'</div>';
    $isCode = !$isCode;
}
?>
<?php else: ?>
<form method="POST" action="save_recor.php" enctype="multipart/form-data">
<textarea name="content"><?= htmlspecialchars($blog['content']) ?></textarea>
<input type="hidden" name="id" value="<?= $blog['id'] ?>">
<div style="margin-top:10px;">
<label>Upload Code File:</label>
<input type="file" name="new_code" accept=".php,.js,.py,.java,.txt">
</div>
<button type="submit" class="save-btn" style="margin-top:10px;">Save</button>
</form>
<?php endif; ?>
</div>

<!-- ✅ Media Section (Image → Audio → Video) -->
<div class="media-container">
    <?php if (!empty($blog['img1'])): ?>
        <img src="<?= htmlspecialchars($blog['img1']) ?>" class="blog-media">
    <?php endif; ?>

    <?php if (!empty($blog['audio'])): ?>
        <audio src="<?= htmlspecialchars($blog['audio']) ?>" controls class="blog-media"></audio>
    <?php endif; ?>

    <?php if (!empty($blog['video'])): ?>
        <video src="<?= htmlspecialchars($blog['video']) ?>" controls class="blog-media"></video>
    <?php endif; ?>
</div>

<?php if (!empty($blog['code'])): 
$codePath = $blog['code'];
if(file_exists($codePath)){ $fileContent = file_get_contents($codePath); $fixedFileContent = str_replace(["\\r\\n","\\r","\\n"],"\n",$fileContent); }
?>
<?php if(!empty($fixedFileContent)): ?>
<div class="code-block">
<h3>Submitted Code:</h3>
<pre><code class="language-php"><?= htmlspecialchars($fixedFileContent) ?></code></pre>
</div>
<?php endif; endif; ?>

<div class="actions">
<form method="POST" style="display:inline;">
<input type="hidden" name="approve_id" value="<?= $blog['id'] ?>">
<button type="submit">Approve</button>
</form>
<!-- <button onclick="rejectBlog(<?= $blog['id'] ?>)">Reject</button> -->
<?php if(!$editMode): ?>
<!-- <a href="?id=<?= $blog['id'] ?>&edit=true"><button>Edit</button></a> -->
<?php endif; ?>
</div>
</div>

<script>
function rejectBlog(id){
if(!confirm("Are you sure you want to reject this blog?")) return;
var formData = new FormData();
formData.append('reject_id', id);

fetch('viewrecor.php', { method:'POST', body:formData })
.then(res => res.json())
.then(data => {
    if(data.status==='success'){
        alert('Blog rejected successfully!');
        document.getElementById('blog-'+id).remove();
    } else {
        alert('Error: '+data.message);
    }
})
.catch(err => alert('Error: '+err));
}
</script>

</body>
</html>
