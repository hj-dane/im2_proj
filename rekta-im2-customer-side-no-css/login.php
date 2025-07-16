<?php
// Set secure session configuration BEFORE session_start()
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Change to 1 if your site is HTTPS
session_start();
require_once 'config.php';

$username = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Optional: Add is_active and is_locked checks
    $stmt = $mysqli->prepare("SELECT id, user_name, user_type_id, password, is_active, is_locked FROM user_login WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $user_type_id, $hashed_password, $is_active, $is_locked);
        $stmt->fetch();

        if (!$is_active) {
            $error_message = "Your account is inactive.";
        } elseif ($is_locked) {
            $error_message = "Your account is locked.";
        } elseif (password_verify($password, $hashed_password)) {
            // ✅ Correct session key used
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $name;
            $_SESSION['user_type_desc'] = $user_type_id;

            header("Location: index.php");
            exit;
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Username not found.";
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">Login</h2>

        <?php if (!empty($_GET['registered'])): ?>
            <div class="alert alert-success text-center">
                Account created successfully. You can now log in.
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-dark w-100">Login</button>
        </form>

        <p class="mt-3 text-center">Not a member? <a href="register.php">Register Now</a></p>
    </div>
</div>
</body>
</html>
