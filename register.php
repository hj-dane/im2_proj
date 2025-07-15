<?php
require_once 'config.php';

$username = $email = $contact_number = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);

    // Validate inputs
    if (strlen($password) < 6 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = "Password must be at least 6 characters and include both letters and numbers.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO user_login (user_name, password, email, contact_number) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $contact_number);

        if ($stmt->execute()) {
            echo "<div class='success-msg'>Signup successful! <a href='login.php'>Login here</a></div>";
            exit;
        } else {
            $errors[] = "Signup failed: " . $stmt->error;
        }

        $stmt->close();
        $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Form</title>
    <link rel="stylesheet" type="text/css" href="css/register.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
	<script src="https://kit.fontawesome.com/a81368914c.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
       
</head>
<body>

    <img class="wave" src="img/wave.png">
    <div class="container">
            <div class="img">
                <img src="img/bg.svg">
            </div>
            
    <div class="signup-container">
        <h2>Signup Form</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-msg">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required />
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required />
            <input type="text" name="contact_number" placeholder="Contact Number" value="<?= htmlspecialchars($contact_number) ?>" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="password" name="confirm_password" placeholder="Confirm Password" required />
            <input type="submit" value="Sign Up" />
        </form>
    </div>
<script src="js/user_login.js"></script>
</body>
</html>
