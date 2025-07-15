<?php
session_start();
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Cart logic
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart (from accessories/clothing)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $product_type = $_POST['product_type']; // 'accessory' or 'clothing'
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    // Fetch product details
    $table = $product_type === 'clothing' ? 'product_inventory' : 'product_inventory'; // You may want to separate tables
    $stmt = $pdo->prepare("SELECT id, product_name AS name, unit_price AS price, image FROM $table WHERE id = ? LIMIT 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $cart_item = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'type' => $product_type
        ];
        // Add or update cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $cart_item['id'] && $item['type'] == $cart_item['type']) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = $cart_item;
        }
    }
    header('Location: cart.php');
    exit;
}

// Remove from cart
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($remove_id) {
        return $item['id'] != $remove_id;
    });
    header('Location: cart.php');
    exit;
}

// Cart display
$cart_items = $_SESSION['cart'];
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKTA | My Cart</title>
    <link rel="stylesheet" href="../styles/cart.css">
</head>
<body>
<header>
    <div class="container">
        <div class="nav-container">
            <div class="logo">REKTA Cycling</div>
            <div class="nav-right">
                <nav>
                    <ul>
                        <li><a href="#">Men</a></li>
                        <li><a href="#">Women</a></li>
                        <li><a href="#">Kids</a></li>
                        <li><a href="clothing.php">Clothing</a></li>
                        <li><a href="accessories.php">Accessories</a></li>
                        <li><a href="cart.php" class="active"><img src="../assets/cart-73-16.png" alt="Cart" style="height:1em;vertical-align:middle;margin-right:6px;">Cart</a></li>
                        <li><a href="favorites.php">Favorites</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <h1>My Cart</h1>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-img"></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    <td><a href="cart.php?remove=<?php echo $item['id']; ?>" class="remove-btn">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-total">
            <h3>Total: ₱<?php echo number_format($total, 2); ?></h3>
            <button class="checkout-btn">Checkout</button>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        &copy; 2025 REKTA Cycling. All rights reserved.
    </div>
</footer>
</body>
</html>
