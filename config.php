<?php
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Optional: Set charset to UTF-8
$mysqli->set_charset("utf8mb4");
?>