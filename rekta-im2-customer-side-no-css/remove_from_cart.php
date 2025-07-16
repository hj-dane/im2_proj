<?php
session_start();
require_once 'config.php';

// ✅ 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$trans_detail_id = $_POST['trans_detail_id'] ?? null;

// ✅ 2. Validate input
if (!$trans_detail_id) {
    echo "Missing item ID.";
    exit;
}

// ✅ 3. Check if item belongs to this user
$stmt = $mysqli->prepare("
    SELECT th.customer_id
    FROM trans_details td
    JOIN trans_header th ON td.trans_header_id = th.id
    JOIN customer c ON th.customer_id = c.id
    WHERE td.id = ? AND c.user_id = ?
");
$stmt->bind_param("ii", $trans_detail_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    echo "❌ Item not found or access denied.";
    exit;
}
$stmt->close();

// ✅ 4. Delete item from cart
$stmt = $mysqli->prepare("DELETE FROM trans_details WHERE id = ?");
$stmt->bind_param("i", $trans_detail_id);
$stmt->execute();
$stmt->close();

// ✅ 5. Redirect back to cart
header("Location: add_to_cart.php");
exit;
