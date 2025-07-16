<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'rekta2';

$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Optional: Set charset to UTF-8
$mysqli->set_charset("utf8mb4");
?>
