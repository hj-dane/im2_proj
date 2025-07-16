<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Missing product ID.']);
    exit;
}

// Get customer_id
$stmt = $mysqli->prepare("SELECT id FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($customer_id);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
    exit;
}
$stmt->close();

// Check if product already in favorites
$stmt = $mysqli->prepare("SELECT id FROM favorites WHERE customer_id = ? AND product_id = ?");
$stmt->bind_param("ii", $customer_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already in favorites.']);
    exit;
}
$stmt->close();

// Insert into favorites
$stmt = $mysqli->prepare("INSERT INTO favorites (customer_id, product_id) VALUES (?, ?)");
$stmt->bind_param("ii", $customer_id, $product_id);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'message' => 'Added to favorites!'
]);
