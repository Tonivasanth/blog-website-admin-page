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

// ✅ Get total admins
$adminCountResult = $conn->query("SELECT COUNT(*) AS total FROM admindetails");
$adminCount = ($adminCountResult && $adminCountResult->num_rows > 0) 
    ? $adminCountResult->fetch_assoc()['total'] : 0;

// ✅ Get total approved blogs
$approvedCountResult = $conn->query("SELECT COUNT(*) AS total FROM approved_blogs");
$approvedCount = ($approvedCountResult && $approvedCountResult->num_rows > 0) 
    ? $approvedCountResult->fetch_assoc()['total'] : 0;

// ✅ Get total error/recheck blogs
$errorCountResult = $conn->query("SELECT COUNT(*) AS total FROM errorcode");
$errorCount = ($errorCountResult && $errorCountResult->num_rows > 0) 
    ? $errorCountResult->fetch_assoc()['total'] : 0;

// ✅ Get total recorrection blogs
$recorrectCountResult = $conn->query("SELECT COUNT(*) AS total FROM recorrection");
$recorrectCount = ($recorrectCountResult && $recorrectCountResult->num_rows > 0) 
    ? $recorrectCountResult->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Calculations</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        .container {
            max-width: 650px;
            margin: 50px auto;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background: #333;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>ADMIN CALCULATION DASHBOARD</h1>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

<div class="container">
    <h2>System Overview</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Admins</td>
                <td><?= $adminCount ?></td>
            </tr>
            <tr>
                <td>Approved Blogs</td>
                <td><?= $approvedCount ?></td>
            </tr>
            <tr>
                <td>Error/Recheck Blogs</td>
                <td><?= $errorCount ?></td>
            </tr>
            <tr>
                <td>Recorrection Blogs</td>
                <td><?= $recorrectCount ?></td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
