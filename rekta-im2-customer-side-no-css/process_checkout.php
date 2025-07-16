<?php
session_start();
require_once 'config.php';
include 'navbar.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "❌ You must be logged in.";
    exit;
}

// Get customer ID
$stmt = $mysqli->prepare("SELECT id FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($customer_id);
if (!$stmt->fetch()) {
    echo "❌ Customer not found.";
    exit;
}
$stmt->close();

// Get open cart (type_id 1)
$trans_type_cart = 1;
$stmt = $mysqli->prepare("SELECT id FROM trans_header WHERE customer_id = ? AND trans_type_id = ?");
$stmt->bind_param("ii", $customer_id, $trans_type_cart);
$stmt->execute();
$stmt->bind_result($trans_header_id);
if (!$stmt->fetch()) {
    $stmt->close();
    echo "❌ No items to checkout.";
    exit;
}
$stmt->close();

// Calculate total
$stmt = $mysqli->prepare("SELECT SUM(amount) FROM trans_details WHERE trans_header_id = ?");
$stmt->bind_param("i", $trans_header_id);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

// Update header: set as checkout type (type_id = 2)
$trans_type_checkout = 2;
$payment_method_id = 1; // default to Cash, change if needed
$delivery_type_id = 1;  // default to Self-Pick up

$stmt = $mysqli->prepare("UPDATE trans_header 
    SET trans_type_id = ?, total_order_amount = ?, amount_paid = ?, payment_method_id = ?, delivery_type_id = ?, trans_date = CURDATE()
    WHERE id = ?");
$stmt->bind_param("idddii", $trans_type_checkout, $total, $total, $payment_method_id, $delivery_type_id, $trans_header_id);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0f1f0eb.js" crossorigin="anonymous"></script>
    <style>
        body { padding-bottom: 80px; }
        footer {
            background: #f8f9fa;
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 10px 0;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>



<!-- ✅ CONTENT -->
<div class="container mt-5">
    <div class="alert alert-success">
        ✅ Checkout successful! Thank you for your purchase.
    </div>

    <div class="text-center mt-4">
        <a href="order_history.php?trans_id=<?= $trans_header_id ?>" class="btn btn-outline-primary">🧾 View Order</a>
        <a href="index.php" class="btn btn-outline-secondary">🔙 Back to Products</a>
    </div>
</div>

<!-- 🔸 FOOTER -->
<footer>
    <div class="container">
        <small>&copy; <?= date('Y') ?> C1Link Shop. All rights reserved.</small>
    </div>
</footer>

</body>
</html>
