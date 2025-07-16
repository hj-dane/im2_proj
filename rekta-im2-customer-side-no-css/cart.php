<?php
session_start();
require_once 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get customer ID
$stmt = $mysqli->prepare("SELECT id FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($customer_id);
if (!$stmt->fetch()) {
    echo "Customer not found.";
    exit;
}
$stmt->close();

// Get existing cart (trans_type_id = 1)
$cart_type_id = 1;
$stmt = $mysqli->prepare("SELECT id FROM trans_header WHERE customer_id = ? AND trans_type_id = ?");
$stmt->bind_param("ii", $customer_id, $cart_type_id);
$stmt->execute();
$stmt->bind_result($trans_header_id);
$has_cart = $stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        footer { background: #222; color: #fff; padding: 40px 0; margin-top: 50px; }
        footer h5 { color: #fff; }
    </style>
</head>
<body>


<div class="container mt-5">
    <h2 class="mb-4">🛒 Your Shopping Cart</h2>

    <?php if (!$has_cart): ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Color</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th style="width: 140px;">Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                $stmt = $mysqli->prepare("
                    SELECT td.id AS trans_detail_id, td.qty_out, td.price, td.amount,
                           p.product_name, p.color, p.size
                    FROM trans_details td
                    JOIN product_inventory p ON td.product_id = p.id
                    WHERE td.trans_header_id = ?
                ");
                $stmt->bind_param("i", $trans_header_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()):
                    $subtotal = $row['amount'];
                    $total += $subtotal;
                ?>
                <tr data-id="<?= $row['trans_detail_id'] ?>">
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['color']) ?></td>
                    <td><?= htmlspecialchars($row['size']) ?></td>
                    <td>₱<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <input type="number" class="form-control quantity-input" data-id="<?= $row['trans_detail_id'] ?>" value="<?= $row['qty_out'] ?>" min="1">
                    </td>
                    <td class="subtotal">₱<?= number_format($subtotal, 2) ?></td>
                    <td>
                        <form method="POST" action="remove_from_cart.php" onsubmit="return confirm('Remove this item?');">
                            <input type="hidden" name="trans_detail_id" value="<?= $row['trans_detail_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2" id="cart-total"><strong>₱<?= number_format($total, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-between mt-3">
            <a href="index.php" class="btn btn-outline-secondary">⬅️ Continue Shopping</a>
            <a href="checkout.php" class="btn btn-success">✅ Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <small>&copy; <?= date('Y') ?> Rekta Shop. All rights reserved.</small>
    </div>
</footer>

<!-- ✅ AJAX: Update quantity -->
<script>
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function () {
        const id = this.dataset.id;
        const qty = this.value;

        fetch('update_quantity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ trans_detail_id: id, quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Optional: for simplicity
            } else {
                alert(data.message);
            }
        });
    });
});
</script>

</body>
</html>
