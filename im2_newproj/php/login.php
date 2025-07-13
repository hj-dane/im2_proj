<?php
require_once "config.php";
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($data['username']) || empty($data['password'])) {
        $response['message'] = 'Please fill all fields';
        echo json_encode($response);
        exit;
    }
    
    // Modified to check user_accounts table
    $sql = "SELECT u.user_id, u.user_name, u.user_type_id, t.title as role 
            FROM user_accounts u
            JOIN user_type t ON u.user_type_id = t.user_type_id
            WHERE u.user_name = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $data['username']);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $user_type_id, $role);
                if ($stmt->fetch()) {
                    // In a real implementation, you would verify password here
                    // For now we'll just assume successful login
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = strtolower($role);
                    $response['success'] = true;
                    $response['user'] = ['role' => strtolower($role)];
                }
            } else {
                $response['message'] = 'Username not found';
            }
        }
        $stmt->close();
    }
    $mysqli->close();
}
echo json_encode($response);
?>