<?php
// admin_messages.php
session_start();

// ✅ Optional: Check admin login
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit;
}

// Connect to review database
$conn = new mysqli("localhost", "root", "", "review");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch messages
$sql = "SELECT id, name, email, thought, created_at FROM message ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Messages</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }

    h1 {
      text-align: center;
      color: #4e54c8;
      margin-bottom: 20px;
    }

    table {
      width: 90%;
      margin: 0 auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
    }

    th {
      background: #4e54c8;
      color: white;
      font-size: 16px;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    tr:hover {
      background: #eef2ff;
    }

    td {
      font-size: 14px;
      color: #333;
    }

    .no-data {
      text-align: center;
      padding: 20px;
      color: #666;
      font-style: italic;
    }

    .back-btn {
      display: block;
      width: fit-content;
      margin: 20px auto;
      padding: 10px 20px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: 0.3s;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #5a67d8, #6b46c1);
      transform: scale(1.05);
    }
  </style>
</head>
<body>

  <h1>📩 Messages From User </h1>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Submitted At</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td><?php echo nl2br(htmlspecialchars($row['thought'])); ?></td>
          <td><?php echo $row['created_at']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p class="no-data">No messages found.</p>
  <?php endif; ?>

  <a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

</body>
</html>
<?php $conn->close(); ?>
