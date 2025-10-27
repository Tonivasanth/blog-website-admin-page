<?php
include 'db_connect.php';

// Fetch all approved blogs
$result = $conn->query("SELECT * FROM approved_blogs ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approved Blogs</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 20px; }
    .blog-card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
    }
    .blog-card h2 { margin-top: 0; color: #333; }
    .blog-card small { color: #777; }
    .blog-card img { max-width: 100%; margin-top: 10px; border-radius: 8px; }
    .blog-card pre { background: #eee; padding: 10px; border-radius: 5px; overflow-x: auto; }
  </style>
</head>
<body>

<h1>Approved Blogs</h1>

<?php while ($row = $result->fetch_assoc()): ?>
  <div class="blog-card">
    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
    <small>By <?php echo htmlspecialchars($row['email']); ?> | 
      <?php echo htmlspecialchars($row['submitted_at']); ?></small>
    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

    <?php if (!empty($row['image_path'])): ?>
      <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Blog Image">
    <?php endif; ?>

    <?php if (!empty($row['code_file'])): ?>
      <pre><?php echo htmlspecialchars(file_get_contents($row['code_file'])); ?></pre>
    <?php endif; ?>

    <?php if (!empty($row['audio_file'])): ?>
      <audio controls>
        <source src="<?php echo htmlspecialchars($row['audio_file']); ?>" type="audio/mpeg">
      </audio>
    <?php endif; ?>

    <?php if (!empty($row['video_file'])): ?>
      <video width="400" controls>
        <source src="<?php echo htmlspecialchars($row['video_file']); ?>" type="video/mp4">
      </video>
    <?php endif; ?>
  </div>
<?php endwhile; ?>

</body>
</html>
