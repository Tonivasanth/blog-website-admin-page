<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Session timeout (30 minutes)
if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 1800) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php?msg=session_expired");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

include 'db_connect.php';

// ✅ Fetch posts from review.recorrection table
$result = $conn->query("SELECT * FROM review.recorrection ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Recheck Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
<div class="header">
    <h1>ADMIN BLOG RECHECK DASHBOARD</h1>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <!-- 🔗 Click title → goes to viewrecor.php -->
                        <form action="viewrecor.php" method="get">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button class="title-btn" type="submit">
                                <?= htmlspecialchars($row['heading']) ?>
                            </button>
                        </form>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No blogs found in recorrection.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
