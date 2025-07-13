<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'admin.dcism.org'; // Try this if admin.dcism.org fails
$username = 's11820346';
$password = 'SEULRENE_kangseulgi';
$dbname = 's11820346_im2';

// Try both connection methods
try {
    echo "<h3>Testing Database Connection</h3>";
    
    // Method 1: MySQLi
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("MySQLi Connection failed: " . $conn->connect_error);
    }
    echo "<p>MySQLi connected successfully</p>";
    
    // Method 2: PDO
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        echo "<p>PDO connected successfully</p>";
    } catch (PDOException $e) {
        echo "<p>PDO connection failed: " . $e->getMessage() . "</p>";
    }
    
    // Test query
    $sql = "SELECT COUNT(*) as count FROM product_inventory";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    echo "<p>Found {$row['count']} products in inventory</p>";
    
    // Show first product
    $sql = "SELECT * FROM product_inventory LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<h4>First Product Sample:</h4>";
        echo "<pre>" . print_r($result->fetch_assoc(), true) . "</pre>";
    } else {
        echo "<p>No products found in database</p>";
    }
    
} catch (Exception $e) {
    die("<div class='alert alert-danger'><h3>Error</h3><p>" . $e->getMessage() . "</p></div>");
}
?>