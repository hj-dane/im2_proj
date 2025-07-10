<?php
session_start();

$host = 'admin.dcism.org';
$user = 's11820346';
$pass = 'SEULRENE_kangseulgi';
$db = 's11820346_im2';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid request data']);
        exit;
    }

    if (isset($data['login'])) {
        $response = ['success' => false, 'message' => ''];

        if (empty($data['username']) || empty($data['password'])) {
            $response['message'] = 'Please fill all fields';
            echo json_encode($response);
            exit;
        }

        $sql = "SELECT u.id, u.user_name, u.password, u.user_type_id, t.user_type, u.is_locked, u.locked_until 
                FROM user_login u
                JOIN user_type t ON u.user_type_id = t.id
                WHERE u.user_name = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $data['username']);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password, $user_type_id, $user_type, $is_locked, $locked_until);
                    if ($stmt->fetch()) {
                        if ($is_locked && strtotime($locked_until) > time()) {
                            $response['message'] = 'Account locked. Try again after ' . date('H:i:s', strtotime($locked_until));
                            echo json_encode($response);
                            exit;
                        }

                        if (password_verify($data['password'], $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = strtolower($user_type);

                            $update_stmt = $mysqli->prepare("UPDATE user_login SET last_login = NOW(), login_attempts = 0, is_locked = 0 WHERE id = ?");
                            if ($update_stmt) {
                                $update_stmt->bind_param("i", $id);
                                $update_stmt->execute();
                                $update_stmt->close();
                            }

                            $response['success'] = true;
                            $response['user'] = [
                                'id' => $id,
                                'username' => $username,
                                'role' => strtolower($user_type)
                            ];
                        } else {
                            $response['message'] = 'Invalid username or password';
                            $attempt_stmt = $mysqli->prepare("UPDATE user_login SET login_attempts = login_attempts + 1 WHERE id = ?");
                            if ($attempt_stmt) {
                                $attempt_stmt->bind_param("i", $id);
                                $attempt_stmt->execute();
                                $attempt_stmt->close();
                            }

                            $check_stmt = $mysqli->prepare("SELECT login_attempts FROM user_login WHERE id = ?");
                            if ($check_stmt) {
                                $check_stmt->bind_param("i", $id);
                                $check_stmt->execute();
                                $check_stmt->bind_result($attempts);
                                $check_stmt->fetch();
                                $check_stmt->close();

                                if ($attempts >= 5) {
                                    $lock_stmt = $mysqli->prepare("UPDATE user_login SET is_locked = 1, locked_until = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE id = ?");
                                    if ($lock_stmt) {
                                        $lock_stmt->bind_param("i", $id);
                                        $lock_stmt->execute();
                                        $lock_stmt->close();
                                        $response['message'] = 'Account locked. Try again after 30 minutes.';
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $response['message'] = 'Username not found';
                }
            } else {
                $response['message'] = 'Database error';
            }
            $stmt->close();
        } else {
            $response['message'] = 'Database error';
        }

        echo json_encode($response);
        exit;

    } else {
        $response = ['success' => false, 'message' => '', 'errors' => []];

        if (empty(trim($data["username"]))) {
            $response['errors']['username'] = "Please enter a username.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($data["username"]))) {
            $response['errors']['username'] = "Username can only contain letters, numbers, and underscores.";
        } else {
            $sql = "SELECT id FROM user_login WHERE user_name = ?";
            if ($stmt = $mysqli->prepare($sql)) {
                $param_username = trim($data["username"]);
                $stmt->bind_param("s", $param_username);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) {
                        $response['errors']['username'] = "This username is already taken.";
                    }
                } else {
                    $response['errors']['username'] = "Database error checking username.";
                }
                $stmt->close();
            }
        }

        if (empty(trim($data["password"]))) {
            $response['errors']['password'] = "Please enter a password.";
        } elseif (strlen(trim($data["password"])) < 6) {
            $response['errors']['password'] = "Password must have at least 6 characters.";
        }

        if (empty(trim($data["confirm_password"]))) {
            $response['errors']['confirm_password'] = "Please confirm password.";
        } elseif ($data["password"] != $data["confirm_password"]) {
            $response['errors']['confirm_password'] = "Passwords did not match.";
        }

        if (empty($response['errors'])) {
            $sql = "INSERT INTO user_login (user_name, password, user_type_id, date_registered, is_active) VALUES (?, ?, ?, NOW(), 1)";
            if ($stmt = $mysqli->prepare($sql)) {
                $param_username = trim($data["username"]);
                $param_password = password_hash($data["password"], PASSWORD_DEFAULT);
                $param_role = 1;

                $stmt->bind_param("ssi", $param_username, $param_password, $param_role);

                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;

                    $sql_customer = "INSERT INTO customer (customer_name, user_id, date_registered) VALUES (?, ?, NOW())";
                    if ($stmt_customer = $mysqli->prepare($sql_customer)) {
                        $stmt_customer->bind_param("si", $param_username, $user_id);
                        if ($stmt_customer->execute()) {
                            $response['success'] = true;
                            $response['message'] = "Registration successful!";
                            $response['user'] = [
                                'id' => $user_id,
                                'username' => $param_username,
                                'role' => 'customer'
                            ];
                        } else {
                            $response['message'] = "Error creating customer record.";
                        }
                        $stmt_customer->close();
                    } else {
                        $response['message'] = "Error preparing customer statement.";
                    }
                } else {
                    $response['message'] = "Oops! Something went wrong. Please try again later.";
                }
                $stmt->close();
            } else {
                $response['message'] = "Error preparing statement.";
            }
        } else {
            $response['message'] = "Please fix the errors below.";
        }

        echo json_encode($response);
        exit;
    }
}
?>

<!-- HTML Below -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="styles/sign.css">
  <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
  <title>Login or Signup</title>
  <style>
    .error-message {
      color: red;
      margin-top: 10px;
      min-height: 20px;
    }
  </style>
</head>
<body>
    <div class="container">
      <div class="forms-container">
          <div class="signin-signup">
              <!-- Login Form -->
              <form action="#" method="POST" class="sign-in-form">
                  <h2 class="title">Sign in</h2>
                  <div class="input-field">
                      <i class="fas fa-user"></i>
                      <input type="text" name="username" placeholder="Username" required>
                  </div>
                  <div class="input-field">
                      <i class="fas fa-lock"></i>
                      <input type="password" name="password" placeholder="Password" required>
                  </div>
                  <input type="submit" value="Login" class="btn solid">
                  <div class="error-message" style="color: red; margin-top: 10px;"></div>
                  <p>Or go back to our <a href="#">Homepage</a>!</p>
              </form>

              <!-- Registration Form -->
              <form action="#" method="POST" class="sign-up-form">
                  <h2 class="title">Sign up</h2>
                  <div class="input-field">
                      <i class="fas fa-user"></i>
                      <input type="text" name="username" placeholder="Username" required>
                  </div>
                  <div class="input-field">
                      <i class="fas fa-envelope"></i>
                      <input type="contact" name="contact" placeholder="Contact Number" required>
                  </div>
                  <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="address" name="address" placeholder="Address" required>
                  </div>
                  <div class="input-field">
                      <i class="fas fa-lock"></i>
                      <input type="password" name="password" placeholder="Password" required>
                  </div>
                  <div class="input-field">
                      <i class="fas fa-lock"></i>
                      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                  </div>
                  <input type="submit" value="Sign Up" class="btn solid">
                  <div class="error-message" style="color: red; margin-top: 10px;"></div>
                  <p>Or go back to our <a href="#">Homepage</a>!</p>
              </form>
          </div>
          
          <div class="panels-container">
              <div class="panel left-panel">
                  <div class="content">
                      <h3>New user?</h3>
                      <br>
                      <p>Join us today and unlock a world of possibilities. Signing up is quick, easy, and gives you access to all our features!</p>
                      <br>
                      <button class="btn transparent" id="sign-up-btn">Sign up</button>
                  </div>
                  <img src="img/log.svg" alt="" class="image">
              </div>
              <div class="panel right-panel">
                  <div class="content">
                      <h3>One of us?</h3>
                      <br>
                      <p>Welcome back! Sign in to continue exploring, sharing, and connecting with our awesome community.</p>
                      <br>
                      <button class="btn transparent" id="sign-in-btn">Sign in</button>
                  </div>
                  <img src="img/register.svg" alt="" class="image">
              </div>
          </div>
      </div>
  </div>
        <script src="js/sign3.js"></script>
        <script type="text/javascript" src="js/new_sign_in.js" defer></script>
  </body>
</body>
</html>
