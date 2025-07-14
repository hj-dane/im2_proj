<?php
$mysqli = new mysqli("localhost", "root", "", "school_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected to local database!";
?>
