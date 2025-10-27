<?php
$host = 'localhost';
$user = 'root';        // Default in XAMPP is 'root'
$pass = '';            // Default password is empty for root
$db   = 'review';      // Your actual database name (the one with blog tables)

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
