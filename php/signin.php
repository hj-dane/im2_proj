<?php
require_once "config.php";
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'errors' => []];
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($data["username"]))) {
        $response['errors']['username'] = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($data["username"]))) {
        $response['errors']['username'] = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT user_id FROM user_accounts WHERE user_name = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($data["username"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $response['errors']['username'] = "This username is already taken.";
                }
            }
            $stmt->close();
        }
    }

    // Validate contact
    if (empty(trim($data["contact"]))) {
        $response['errors']['contact'] = "Please enter a contact number.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', trim($data["contact"]))) {
        $response['errors']['contact'] = "Invalid contact number format.";
    }

    // Validate address
    if (empty(trim($data["address"]))) {
        $response['errors']['address'] = "Please enter an address.";
    }

    // Validate role (maps to user_type_id)
    $valid_roles = [
        'customer' => 1, 
        'seller' => 2,
        'administrator' => 3
    ];
    
    if (empty($data["role"]) || !array_key_exists($data["role"], $valid_roles)) {
        $response['errors']['role'] = "Please select a valid role.";
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

    if (empty($response['errors'])) {
        // First insert into user_accounts
        $sql = "INSERT INTO user_accounts (user_name, contact_number, address, user_type_id) VALUES (?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("sisi", 
                $param_username,
                $param_contact,
                $param_address,
                $param_role
            );
            
            $param_username = trim($data["username"]);
            $param_contact = (int)trim($data["contact"]);
            $param_address = trim($data["address"]);
            $param_role = $valid_roles[$data["role"]];
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                // Then insert into customer table if role is customer
                if ($data["role"] == 'customer') {
                    $sql_customer = "INSERT INTO customer (customer_id, customer_name, contact_number, address) VALUES (?, ?, ?, ?)";
                    if ($stmt_customer = $mysqli->prepare($sql_customer)) {
                        $stmt_customer->bind_param("isis",
                            $user_id,
                            $param_username,
                            $param_contact,
                            $param_address
                        );
                        $stmt_customer->execute();
                        $stmt_customer->close();
                    }
                }
                
                $response['success'] = true;
                $response['message'] = "Registration successful!";
            } else {
                $response['message'] = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    } else {
        $response['message'] = "Please fix the errors below.";
    }
    
    $mysqli->close();
    echo json_encode($response);
    exit();
}
?>