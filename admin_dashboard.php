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

// ✅ Check if new messages arrived in last 24 hours
$newMsgResult = $conn->query("
    SELECT COUNT(*) AS cnt 
    FROM message 
    WHERE created_at >= NOW() - INTERVAL 1 DAY
");
$newMsg = ($newMsgResult && $newMsgResult->num_rows > 0) 
    ? $newMsgResult->fetch_assoc()['cnt'] 
    : 0;

// ✅ Fetch correct fields based on table
if (isset($_GET['view']) && $_GET['view'] === "approved") {
    $result = $conn->query("
        SELECT id, heading, created_at 
        FROM approved_blogs 
        ORDER BY created_at DESC
    ");
    $isApproved = true;
} else {
    $result = $conn->query("
        SELECT id, heading, created_at 
        FROM userss
        ORDER BY created_at DESC
    ");
    $isApproved = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css?v=<?php echo time(); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .message-button {
        position: relative;
    }
    .message-dot {
        position: absolute;
        top: -5px;
        right: -5px;
        height: 12px;
        width: 12px;
        background-color: violet;
        border-radius: 50%;
        border: 2px solid white;
    }
    .popup {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: #fff;
        font-weight: bold;
        z-index: 1000;
        animation: fadeIn 0.5s ease-in-out;
    }
    .popup.success { background-color: #5eb925ff; } /* Green */
    .popup.error { background-color: #dc3545; }   /* Red */

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
</head>
<body>
<div class="header">
    <h1>ADMIN DASHBOARD</h1>
    <div class="header-buttons">
        <a href="admin_messages.php" class="message-button">
            <i class="fas fa-envelope"></i> Messages
            <?php if ($newMsg > 0): ?>
                <span class="message-dot"></span>
            <?php endif; ?>
        </a>
        <a href="admin_recheck.php" class="recheck-page">
            <i class="fas fa-sync-alt"></i> Recheck
        </a>
        <a href="admin_calculation.php?view=approved" class="approval-button">
            <i class="fas fa-check-circle"></i> Admin-Overview
        </a>
        <a href="logout.php" class="logout-button">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<!-- Old Notifications -->
<div class="notification">
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
</div>

<!-- ✅ New Popup Notifications -->
<div class="notification">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="popup success"><?= $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="popup error"><?= $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</div>

<script>
    // Auto-hide popup after 3 seconds
    setTimeout(() => {
        const popup = document.querySelector('.popup');
        if (popup) {
            popup.style.transition = "opacity 0.5s ease";
            popup.style.opacity = "0";
            setTimeout(() => popup.remove(), 500);
        }
    }, 3000);
</script>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <form action="view_blog.php" method="get">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button class="title-btn" type="submit">
                            <?= htmlspecialchars($row['heading']) ?>
                        </button>
                    </form>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if (!$isApproved): ?>
                        <!-- Approve button -->
                        <form method="POST" action="approve_blog.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button class="approve-btn" type="submit">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- Remove button -->
                        <form method="POST" action="delete_blog.php" style="display:inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this blog?');">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="submit" class="btn-delete">🗑 Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
