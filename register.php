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
            echo "Signup successful! <a href='login.php'>Login here</a>";
            exit;
        } else {
            $errors[] = "Signup failed: " . $stmt->error;
        }

        $stmt->close();
        $mysqli->close();
    }
}
?>

<h2>Signup Form</h2>

<?php if (!empty($errors)): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    Username: <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required /><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required /><br>
    Contact Number: <input type="text" name="contact_number" value="<?= htmlspecialchars($contact_number) ?>" required /><br>
    Password: <input type="password" name="password" required /><br>
    Confirm Password: <input type="password" name="confirm_password" required /><br>
    <input type="submit" value="Sign Up" />
</form>
