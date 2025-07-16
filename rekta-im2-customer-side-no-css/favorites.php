<?php
session_start();
require_once 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_logged_in = true;

// Get cart count
$cart_count = 0;
$stmt = $mysqli->prepare("SELECT id FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($customer_id);
if ($stmt->fetch()) {
    $stmt->close();
    $stmt = $mysqli->prepare("SELECT id FROM trans_header WHERE customer_id = ? AND trans_type_id = 1");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->bind_result($trans_id);
    if ($stmt->fetch()) {
        $stmt->close();
        $stmt = $mysqli->prepare("SELECT SUM(qty_out) FROM trans_details WHERE trans_header_id = ?");
        $stmt->bind_param("i", $trans_id);
        $stmt->execute();
        $stmt->bind_result($cart_count);
        $stmt->fetch();
    }
}
$stmt->close();

// Get favorite items
$stmt = $mysqli->prepare("SELECT f.product_id, p.product_name, p.unit_price, p.product_description FROM favorites f JOIN product_inventory p ON f.product_id = p.id WHERE f.customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Favorites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
       
        footer h5 { color: #fff; }
    </style>
</head>
<body>


<div class="container mt-5">
    <h2 class="mb-4">❤️ Your Favorites</h2>
    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">You don't have any favorite items yet. <a href="index.php">Check out our products, you might like one</a></div>
    <?php else: ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h5>
                            <p class="card-text">₱<?= number_format($row['unit_price'], 2) ?></p>
                            <p class="card-text small"><?= htmlspecialchars($row['product_description']) ?></p>

                            <form class="add-to-cart-form mb-2">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <small>&copy; <?= date('Y') ?> REKTA. All rights reserved.</small>
    </div>
</footer>

<script>
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const productId = this.querySelector('input[name="product_id"]').value;
        const quantity = this.querySelector('input[name="quantity"]').value;

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&quantity=${quantity}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("✅ " + data.message);
                document.getElementById('cart-count')?.textContent = data.cart_count ?? 1;
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(() => alert("❌ Error adding to cart."));
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
