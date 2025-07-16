<?php
// require_once "config.php";
// header('Content-Type: application/json');

session_start();
// $host = 'c1-link.com';
// $user = 'c1link_aishen';
// $pass = 'SEULRENE_kangseulgi';
// $db = 'c1link_usc_db';
$host = 'localhost';
$user = 'c1link_aishen';
$pass = 'SEULRENE_kangseulgi';
$db = 'c1link_usc_db';

// Handle AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    $mysqli = null;
    try {
        $mysqli = new mysqli($host, $user, $pass, $db);
        
        if ($mysqli->connect_error) {
            throw new Exception("Database connection failed: " . $mysqli->connect_error);
        }
        
        $mysqli->set_charset("utf8mb4");
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data === null) {
            throw new Exception('Invalid request data');
        }
        
        if (isset($data['login'])) {
            handleLogin($mysqli, $data);
        } elseif (isset($data['signup'])) {
            handleSignup($mysqli, $data);
        } else {
            throw new Exception('Invalid request type');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        if ($mysqli) {
            $mysqli->close();
        }
        exit;
    }

    if ($mysqli) {
        $mysqli->close();
    }
    exit;
}

function handleLogin($mysqli, $data) {
    $response = ['success' => false, 'message' => ''];
    
    // Validate input
    if (empty($data['username']) || empty($data['password'])) {
        $response['message'] = 'Please fill all fields';
        echo json_encode($response);
        exit;
    }
    
    $sql = "SELECT id, user_name, password, is_locked, locked_until, login_attempts, is_active
            FROM user_login 
            WHERE user_name = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $data['username']);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                // Initialize variables with proper types
                $id = 0;
                $user_name = '';
                $password = '';
                $is_locked = false;
                $locked_until = null;
                $login_attempts = 0;
                $is_active = false;

                $stmt->bind_result($id, $user_name, $password, $is_locked, $locked_until, $login_attempts, $is_active);
                $stmt->fetch();
                
                // Validate essential fields
                if (empty($user_name) || empty($password)) {
                    $response['message'] = 'Invalid user data in database';
                    echo json_encode($response);
                    exit;
                }
                
                // Check if account is active
                if (!$is_active) {
                    $response['message'] = 'Account is inactive. Please contact support.';
                    echo json_encode($response);
                    exit;
                }
                
                // Check if account is locked
                if ($is_locked && $locked_until && strtotime($locked_until) > time()) {
                    $response['message'] = 'Account locked. Try again after ' . date('M j, Y H:i:s', strtotime($locked_until));
                    echo json_encode($response);
                    exit;
                }
                
                // Verify password
                if (password_verify($data['password'], $password)) {
                    // Login successful - regenerate session ID to prevent fixation
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_regenerate_id(true);
                    }
                    
                    // Set session variables with proper sanitization
                    $_SESSION = array(); // Clear existing session data first
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = (int)$id;
                    $_SESSION["username"] = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
                    $_SESSION["last_activity"] = time();
                    $_SESSION["ip_address"] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION["user_agent"] = $_SERVER['HTTP_USER_AGENT'];
                    
                    // Reset login attempts and update last login
                    $update_sql = "UPDATE user_login 
                                  SET last_login = NOW(), 
                                      login_attempts = 0, 
                                      is_locked = 0,
                                      locked_until = NULL
                                  WHERE id = ?";
                    
                    $update_stmt = $mysqli->prepare($update_sql);
                    if ($update_stmt) {
                        $update_stmt->bind_param("i", $id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                    
                    // Prepare success response
                    $response['success'] = true;
                    $response['redirect'] = "dashboard.php";
                    $response['user'] = [
                        'id' => (int)$id,
                        'username' => htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8')
                    ];
                } else {
                    // Invalid password
                    $response['message'] = 'Invalid username or password';
                    
                    // Increment login attempts
                    $attempts = $login_attempts + 1;
                    $lock_account = $attempts >= 5;
                    $lock_until = $lock_account ? date('Y-m-d H:i:s', strtotime('+30 minutes')) : null;
                    
                    $attempt_sql = "UPDATE user_login 
                                   SET login_attempts = ?,
                                       is_locked = ?,
                                       locked_until = ?
                                   WHERE id = ?";
                    
                    if ($attempt_stmt = $mysqli->prepare($attempt_sql)) {
                        $locked = $lock_account ? 1 : 0;
                        $attempt_stmt->bind_param("iisi", $attempts, $locked, $lock_until, $id);
                        $attempt_stmt->execute();
                        $attempt_stmt->close();
                        
                        if ($lock_account) {
                            $response['message'] = 'Account locked. Try again after 30 minutes.';
                        } else {
                            $remaining_attempts = 5 - $attempts;
                            $response['message'] = "Invalid credentials. {$remaining_attempts} attempts remaining.";
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
}

function handleSignup($mysqli, $data) {
    $response = ['success' => false, 'message' => '', 'errors' => []];
    
    // Validate username
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
    
    // Validate password
    if (empty(trim($data["password"]))) {
        $response['errors']['password'] = "Please enter a password.";
    } elseif (strlen(trim($data["password"])) < 6) {
        $response['errors']['password'] = "Password must have at least 6 characters.";
    }
    
    // Validate confirm password
    if (empty(trim($data["confirm_password"]))) {
        $response['errors']['confirm_password'] = "Please confirm password.";
    } elseif ($data["password"] != $data["confirm_password"]) {
        $response['errors']['confirm_password'] = "Passwords did not match.";
    }
    
    // Validate contact
    if (empty(trim($data["contact"]))) {
        $response['errors']['contact'] = "Please enter contact number.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', trim($data["contact"]))) {
        $response['errors']['contact'] = "Please enter a valid contact number.";
    }
    
    // Validate address
    if (empty(trim($data["address"]))) {
        $response['errors']['address'] = "Please enter address.";
    }
    
    // If no errors, proceed with registration
    if (empty($response['errors'])) {
        $mysqli->begin_transaction();
        
        try {
            // Create user login
            $sql = "INSERT INTO user_login 
                    (user_name, password, date_registered, is_active, login_attempts) 
                    VALUES (?, ?, NOW(), 1, 0)";
            
            if ($stmt = $mysqli->prepare($sql)) {
                $param_username = trim($data["username"]);
                $param_password = password_hash($data["password"], PASSWORD_DEFAULT);
                
                $stmt->bind_param("ss", $param_username, $param_password);
                
                if ($stmt->execute()) {
                    $id = $stmt->insert_id;
                    
                    // Create customer record
                    $sql_customer = "INSERT INTO customer 
                                     (customer_name, contact_number, address, id, date_registered) 
                                     VALUES (?, ?, ?, ?, NOW())";
                    
                    if ($stmt_customer = $mysqli->prepare($sql_customer)) {
                        $contact_number = (int)$data["contact"];
                        $stmt_customer->bind_param("sisi", $param_username, $contact_number, $data["address"], $id);
                        
                        if ($stmt_customer->execute()) {
                            $mysqli->commit();
                            
                            $response['success'] = true;
                            $response['message'] = "Registration successful! You can now login.";
                        } else {
                            throw new Exception("Error creating customer record.");
                        }
                        $stmt_customer->close();
                    } else {
                        throw new Exception("Error preparing customer statement.");
                    }
                } else {
                    throw new Exception("Oops! Something went wrong. Please try again later.");
                }
                $stmt->close();
            } else {
                throw new Exception("Error preparing statement.");
            }
        } catch (Exception $e) {
            $mysqli->rollback();
            $response['message'] = $e->getMessage();
        }
    } else {
        $response['message'] = "Please fix the errors below.";
    }
    
    echo json_encode($response);
    exit;
}

// If not an AJAX request, continue to output HTML
?>

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
        <form id="loginForm" class="sign-in-form">
          <h2 class="title">Sign in</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit" class="btn solid">Login</button>
          <div class="error-message"></div>
          <p>Or go back to our <a href="#">Homepage</a>!</p>
        </form>

        <!-- Registration Form -->
        <form id="signupForm" class="sign-up-form">
          <h2 class="title">Sign up</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
          </div>
          <div class="input-field">
            <i class="fas fa-phone"></i>
            <input type="tel" name="contact" placeholder="Contact Number" required>
          </div>
          <div class="input-field">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" name="address" placeholder="Address" required>
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          </div>
          <button type="submit" class="btn solid">Sign Up</button>
          <div class="error-message"></div>
          <div class="success-message"></div>
          <p>Or go back to our <a href="#">Homepage</a>!</p>
        </form>
      </div>
      
      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>New user?</h3>
            <p>Join us today and unlock a world of possibilities!</p>
            <button class="btn transparent" id="sign-up-btn">Sign up</button>
          </div>
          <img src="img/log.svg" alt="" class="image">
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>One of us?</h3>
            <p>Welcome back! Sign in to continue your experience.</p>
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
