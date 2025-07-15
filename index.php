<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
<h3>Your User Type is: <?= htmlspecialchars($_SESSION['user_type_desc']) ?>!</h3>
<p>You are logged in.</p>
<a href="logout.php">Logout</a>
