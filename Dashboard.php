<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>
Welcome, <?php echo $_SESSION['full_name'] ?? $_SESSION['username']; ?>
</h2>

<p>Role: <?php echo $_SESSION['role']; ?></p>

<a href="logout.php">Logout</a>
