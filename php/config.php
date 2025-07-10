<?php
$servername = "admin.dcism.org";
$username = "s11820346";
$password = "SEULRENE_kangseulgi";
$database = "s11820346_im2";

$mysqli = new mysqli($servername, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Optional: Set charset to UTF-8
$mysqli->set_charset("utf8mb4");
?>