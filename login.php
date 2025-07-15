<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $mysqli->prepare("SELECT id, user_name, user_type_id, password FROM user_login WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $user_type_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $name;
            $_SESSION['id'] = $id;
            $_SESSION['user_type_desc'] = $user_type_id;

            header("Location: index.php");
            // Redirect based on user_type_id
            if ($user_type_id == 1) {
                 $_SESSION['user_type_desc'] = "Customer";
                //  header("Location: customer_form.php");
            } else {
                $_SESSION['user_type_desc'] = "Admin/Seller";
                // header("Location: admin_form.php");
            }            
            // exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Username not found.";
    }

    $stmt->close();
    $mysqli->close();
}
?>

<h2>Login Form</h2>
<form method="POST">
    Username: <input type="text" name="username" required /><br>
    Password: <input type="password" name="password" required /><br>
    <input type="submit" value="Login" />
</form>

<!-- Sign-Up Button -->
<form action="register.php" method="get">
    <p>Don't have an account?</p>
    <button type="submit">Sign Up</button>
</form>
