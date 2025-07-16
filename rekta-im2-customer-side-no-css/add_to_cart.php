<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// ✅ 1. Check user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

// ✅ 2. Check input
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;

if (!$product_id || !$quantity || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid product_id or quantity']);
    exit;
}

// ✅ 3. Get customer_id
$stmt = $mysqli->prepare("SELECT id FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($customer_id);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    exit;
}
$stmt->close();

// ✅ 4. Get or create cart (trans_header with trans_type_id = 1)
$trans_type_id = 1; // 1 means "cart"
$stmt = $mysqli->prepare("SELECT id FROM trans_header WHERE customer_id = ? AND trans_type_id = ?");
$stmt->bind_param("ii", $customer_id, $trans_type_id);
$stmt->execute();
$stmt->bind_result($trans_header_id);
if (!$stmt->fetch()) {
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO trans_header (customer_id, trans_type_id, trans_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $customer_id, $trans_type_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to create cart header']);
        exit;
    }
    $trans_header_id = $stmt->insert_id;
    $stmt->close();
} else {
    $stmt->close();
}

// ✅ 5. Get product price
$stmt = $mysqli->prepare("SELECT unit_price FROM product_inventory WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($price);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}
$stmt->close();

// ✅ 6. Check if product already in cart
$stmt = $mysqli->prepare("SELECT id, qty_out FROM trans_details WHERE trans_header_id = ? AND product_id = ?");
$stmt->bind_param("ii", $trans_header_id, $product_id);
$stmt->execute();
$stmt->bind_result($detail_id, $existing_qty);
if ($stmt->fetch()) {
    // Update existing entry
    $stmt->close();
    $new_qty = $existing_qty + $quantity;
    $amount = $new_qty * $price;
    $stmt = $mysqli->prepare("UPDATE trans_details SET qty_out = ?, amount = ? WHERE id = ?");
    $stmt->bind_param("idi", $new_qty, $amount, $detail_id);
    $stmt->execute();
    $stmt->close();
} else {
    // Insert new row
    $stmt->close();
    $amount = $quantity * $price;
    $stmt = $mysqli->prepare("INSERT INTO trans_details (trans_header_id, product_id, qty_out, price, amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidd", $trans_header_id, $product_id, $quantity, $price, $amount);
    $stmt->execute();
    $stmt->close();
}

// ✅ 7. Get updated cart count
$stmt = $mysqli->prepare("SELECT SUM(qty_out) FROM trans_details WHERE trans_header_id = ?");
$stmt->bind_param("i", $trans_header_id);
$stmt->execute();
$stmt->bind_result($cart_count);
$stmt->fetch();
$stmt->close();

// ✅ 8. Return success response
echo json_encode([
    'success' => true,
    'message' => 'Product added to cart!',
    'cart_count' => $cart_count ?? 1
]);
