<?php
session_start();
require_once 'config.php';
include 'navbar.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
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

// Get current cart
$cart_type = 1;
$stmt = $mysqli->prepare("SELECT id FROM trans_header WHERE customer_id = ? AND trans_type_id = ?");
$stmt->bind_param("ii", $customer_id, $cart_type);
$stmt->execute();
$stmt->bind_result($cart_id);
if (!$stmt->fetch()) {
    echo "❌ No cart found.";
    exit;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0f1f0eb.js" crossorigin="anonymous"></script>
    <style>
        body {
            padding-bottom: 80px;
        }
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


<!-- 🔸 MAIN -->
<div class="container mt-5">
    <h2 class="mb-4">🧾 Checkout</h2>

    <form method="POST" action="process_checkout.php">
        <input type="hidden" name="cart_id" value="<?= $cart_id ?>">

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select name="payment_method_id" id="payment_method" class="form-select" required>
                <option value="">-- Select Payment Method --</option>
                <option value="1">Cash</option>
                <option value="2">Gcash</option>
                <option value="3">Online Transfer</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="delivery_type" class="form-label">Delivery Type</label>
            <select name="delivery_type_id" id="delivery_type" class="form-select" required>
                <option value="">-- Select Delivery Option --</option>
                <option value="1">Self Pick-up</option>
                <option value="2">Self-arranged</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">✅ Confirm & Place Order</button>
        </div>
    </form>
</div>

<!-- 🔹 FOOTER -->
<footer>
    <div class="container">
        <small>&copy; <?= date('Y') ?> C1Link Shop. All rights reserved.</small>
    </div>
</footer>

</body>
</html>
