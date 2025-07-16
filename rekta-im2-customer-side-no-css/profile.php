<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Fetch user profile
$query = "
    SELECT 
        ul.user_name,
        c.customer_name,
        c.email,
        c.contact_number
    FROM user_login ul
    JOIN customer c ON c.user_id = ul.id
    WHERE ul.id = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Rekta Online Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4 text-center">Your Profile</h2>
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h5 class="card-title">Username:</h5>
                <p class="card-text"><?= htmlspecialchars($user['user_name']) ?></p>

                <h5 class="card-title">Full Name:</h5>
                <p class="card-text"><?= htmlspecialchars($user['customer_name']) ?></p>

                <h5 class="card-title">Email:</h5>
                <p class="card-text"><?= htmlspecialchars($user['email']) ?></p>

                <h5 class="card-title">Contact Number:</h5>
                <p class="card-text"><?= htmlspecialchars($user['contact_number']) ?></p>

                <a href="index.php" class="btn btn-dark mt-3">Back to Shop</a>
            </div>
        </div>
    </div>
</body>
</html>
