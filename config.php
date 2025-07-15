<?php
    $host = 'c1-link.com';
    $user = 'c1link_aishen';
    $pass = 'SEULRENE_kangseulgi';
    $db = 'c1link_usc_db';

    $mysqli = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Optional: Set charset to UTF-8
    $mysqli->set_charset("utf8mb4");
?>