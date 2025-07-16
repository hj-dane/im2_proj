<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// ✅ 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ 2. Get and validate JSON input
$input = json_decode(file_get_contents("php://input"), true);
$trans_detail_id = $input['trans_detail_id'] ?? null;
$quantity = (int)($input['quantity'] ?? 0);

if (!$trans_detail_id || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity or transaction detail ID.']);
    exit;
}

// ✅ 3. Check that item exists and belongs to this user
$stmt = $mysqli->prepare("
    SELECT td.product_id, th.customer_id, td.price
    FROM trans_details td
    JOIN trans_header th ON td.trans_header_id = th.id
    JOIN customer c ON th.customer_id = c.id
    WHERE td.id = ? AND c.user_id = ?
");
$stmt->bind_param("ii", $trans_detail_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found or not authorized.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($product_id, $customer_id, $price);
$stmt->fetch();
$stmt->close();

// ✅ 4. Update quantity and amount
$amount = $price * $quantity;

$stmt = $mysqli->prepare("UPDATE trans_details SET qty_out = ?, amount = ? WHERE id = ?");
$stmt->bind_param("idi", $quantity, $amount, $trans_detail_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to update item.']);
    $stmt->close();
    exit;
}
$stmt->close();

// ✅ 5. Recalculate cart total for this user
$stmt = $mysqli->prepare("
    SELECT SUM(amount) FROM trans_details td
    JOIN trans_header th ON td.trans_header_id = th.id
    WHERE th.customer_id = ? AND th.trans_type_id = 1
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($new_total);
$stmt->fetch();
$stmt->close();

// ✅ 6. Return updated values
echo json_encode([
    'success' => true,
    'message' => 'Quantity updated successfully.',
    'subtotal' => $amount,
    'total' => $new_total
]);
