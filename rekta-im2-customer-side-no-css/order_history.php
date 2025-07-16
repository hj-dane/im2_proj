<?php
session_start();
require_once 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get customer ID and name
$stmt = $mysqli->prepare("SELECT id, customer_name FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($customer_id, $customer_name);
if (!$stmt->fetch()) {
    echo "❌ Customer not found.";
    exit;
}
$stmt->close();

// Get all completed orders
$stmt = $mysqli->prepare("SELECT id, trans_date, total_order_amount FROM trans_header WHERE customer_id = ? AND trans_type_id = 2 ORDER BY id DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="mb-4">🧾 Order History</h2>

    <?php if ($orders->num_rows === 0): ?>
        <div class="alert alert-info">You have no completed orders yet.</div>
    <?php else: ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="bg-white p-4 shadow rounded mb-4">
                <h5>Order #<?= $order['id'] ?> <small class="text-muted float-end"><?= $order['trans_date'] ?></small></h5>
                <p><strong>Customer:</strong> <?= htmlspecialchars($customer_name) ?></p>

                <?php
                $stmt_details = $mysqli->prepare("
                    SELECT p.product_name, p.color, p.size, td.qty_out, td.price, td.amount
                    FROM trans_details td
                    JOIN product_inventory p ON td.product_id = p.id
                    WHERE td.trans_header_id = ?
                ");
                $stmt_details->bind_param("i", $order['id']);
                $stmt_details->execute();
                $details = $stmt_details->get_result();
                ?>

                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $details->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['color']) ?></td>
                            <td><?= htmlspecialchars($row['size']) ?></td>
                            <td><?= $row['qty_out'] ?></td>
                            <td>₱<?= number_format($row['price'], 2) ?></td>
                            <td>₱<?= number_format($row['amount'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total:</strong></td>
                            <td><strong>₱<?= number_format($order['total_order_amount'], 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                <!-- <form action="email_receipt.php" method="post" class="mt-3">
                    <input type="hidden" name="trans_header_id" value="<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-outline-primary">📧 Email this receipt</button>
                </form> -->

                <?php $stmt_details->close(); ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary">⬅️ Back to Shop</a>

    </div>
</div>

</body>
</html>
