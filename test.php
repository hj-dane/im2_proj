<?php
// test.php - Simple PHP configuration test file

// 1. Check if PHP is working
echo "<h1>PHP Test Page</h1>";
echo "<p>If you see this, PHP is working!</p>";

// 2. Show PHP configuration info
echo "<h2>PHP Information:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// 3. Test database connection (optional)
$host = 'admin.dcism.org';
$user = 's11820346';
$pass = 'SEULRENE_kangseulgi';
$db = 's11820346_im2';

echo "<h2>Database Connection Test:</h2>";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<p style='color:green'>✓ Successfully connected to organization's database!</p>";
    
    // Optional: List tables to verify access
    $result = $conn->query("SHOW TABLES");
    echo "<h3>Tables in database:</h3>";
    while ($row = $result->fetch_array()) {
        echo "<p>".$row[0]."</p>";
    }
}
// 4. Show all PHP configuration (remove this in production)
echo "<h2>Full PHP Info:</h2>";
echo "<a href='?phpinfo=1'>Show PHP Configuration</a>";

if(isset($_GET['phpinfo'])) {
    phpinfo();
}
?>