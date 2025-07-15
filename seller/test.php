<!-- User Role Label -->
<span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Change these values to your actual DB config
$host = 'localhost';  // or localhost if DB is on same machine
$user = 's11820346';
$pass = 'yourpassword';
$db   = 'yourdatabase';
$port = 3306;  // change if needed

$mysqli = new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    die("❌ Connection failed: " . $mysqli->connect_error);
} else {
    echo "✅ Connected to MySQL successfully!";
}
?>
