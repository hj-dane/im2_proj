<?php
    // $servername = "c1-link.com";
    // $username = "c1link_aishen";
    // $password = "SEULRENE_kangseulgi";
    // $database = "c1link_usc_db";

    $servername = "localhost";
    $username = "c1link_aishen";
    $password = "SEULRENE_kangseulgi";
    $database = "c1link_usc_db";

    $mysqli = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Optional: Set charset to UTF-8
    $mysqli->set_charset("utf8mb4");
?>