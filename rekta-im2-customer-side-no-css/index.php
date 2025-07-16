<?php
session_start();
include 'config.php';

$is_logged_in = isset($_SESSION['loggedin']);
$filter = $_GET['category'] ?? 'all';
$size_filter = $_GET['size'] ?? '';
$color_filter = $_GET['color'] ?? '';
$sort_order = $_GET['sort'] ?? '';
$search_query = $_GET['search'] ?? '';

// Get cart count
$cart_count = 0;
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekta Online Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .navbar { position: sticky; top: 0; z-index: 1030; }
        .navbar .icons a:hover i,
        .navbar .icons button:hover i { outline: 2px solid black; border-radius: 5px; }
        .sidebar { min-width: 250px; padding: 20px; border-right: 1px solid #ccc; }
        .product-list { flex-grow: 1; padding: 20px; }
        .search-bar { display: flex; align-items: center; gap: 8px; }
        .search-bar input[type="text"] { border: 1px solid #ccc; border-radius: 4px; padding: 5px 10px; }
        .search-bar i:hover { outline: 2px solid black; border-radius: 4px; cursor: pointer; }
        footer { background: #222; color: #fff; padding: 40px 0; margin-top: 50px; }
        footer h5 { color: #fff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-black px-3">
    <a class="navbar-brand" href="index.php">REKTA</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="index.php?category=1">Clothing</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?category=2">Accessories</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php">All</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
        </ul>
        <form class="search-bar me-3" method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit" class="btn btn-light p-1"><i class="fa-solid fa-magnifying-glass text-dark"></i></button>
        </form>
        <div class="icons d-flex align-items-center">
            <a href="favorites.php" class="text-white me-3"><i class="fa-solid fa-heart"></i></a>
            <a href="cart.php" class="text-white me-3 position-relative">
                <i class="fa-solid fa-cart-shopping"></i>
                <span id="cart-count" class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                    <?= $cart_count ?? 0 ?>
                </span>
            </a>
            <div class="dropdown me-3">
                <a href="#" class="text-white dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <?php if ($is_logged_in): ?>
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3 sidebar">
            <form method="GET" action="index.php">
                <h5>Filter by Size</h5>
                <?php foreach (["S", "M", "L"] as $size): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="size" value="<?= $size ?>" <?= $size_filter === $size ? 'checked' : '' ?>>
                        <label class="form-check-label">Size <?= $size ?></label>
                    </div>
                <?php endforeach; ?>

                <h5 class="mt-3">Filter by Color</h5>
                <?php foreach (["Black", "White", "Red"] as $color): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="color" value="<?= $color ?>" <?= $color_filter === $color ? 'checked' : '' ?>>
                        <label class="form-check-label">Color <?= $color ?></label>
                    </div>
                <?php endforeach; ?>

                <h5 class="mt-3">Sort by Price</h5>
                <select name="sort" class="form-select">
                    <option value="">Default</option>
                    <option value="asc" <?= $sort_order === 'asc' ? 'selected' : '' ?>>Lowest to Highest</option>
                    <option value="desc" <?= $sort_order === 'desc' ? 'selected' : '' ?>>Highest to Lowest</option>
                </select>

                <button type="submit" class="btn btn-dark mt-3 w-100">Apply Filters</button>
            </form>
        </div>
        <div class="col-md-9 product-list">
            <?php
            $query = "SELECT * FROM product_inventory WHERE is_active = 1";
            if (is_numeric($filter)) $query .= " AND category_id = " . intval($filter);
            if ($size_filter) $query .= " AND size = '" . $mysqli->real_escape_string($size_filter) . "'";
            if ($color_filter) $query .= " AND color = '" . $mysqli->real_escape_string($color_filter) . "'";
            if ($search_query) $query .= " AND product_name LIKE '%" . $mysqli->real_escape_string($search_query) . "%'";
            if ($sort_order === 'asc') $query .= " ORDER BY unit_price ASC";
            elseif ($sort_order === 'desc') $query .= " ORDER BY unit_price DESC";

            $result = $mysqli->query($query);
            echo '<div class="row">';
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card h-100 position-relative'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($row['product_name']) . "</h5>";
                echo "<p class='card-text'>Price: ₱" . number_format($row['unit_price'], 2) . "</p>";
                echo "<p class='card-text'>" . htmlspecialchars($row['product_description']) . "</p>";

                if ($is_logged_in) {
                    echo "<div class='position-absolute top-0 end-0 m-2 d-flex gap-2'>";
                    echo "<button type=\"button\" class=\"btn btn-outline-secondary fav-btn\" data-product-id=\"" . $row['id'] . "\">
                            <i class=\"fa-solid fa-heart\"></i></button>";

                    // ✅ Correct form with product_id and quantity
                    echo "<form class='add-to-cart-form'>";
                    echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                    echo "<input type='hidden' name='quantity' value='1'>";
                    echo "<button type='submit' class='btn btn-outline-dark p-2'><i class='fa-solid fa-cart-shopping'></i></button>";
                    echo "</form>";

                    echo "</div>";
                } else {
                    echo "<button class='btn btn-secondary w-100 mb-2' disabled>Add to Cart</button>";
                    echo "<button class='btn btn-secondary w-100' disabled>Add to Favorites</button>";
                }

                echo "</div></div></div>";

            }
            echo '</div>';
            ?>
        </div>
    </div>
</div>

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
                document.getElementById('cart-count').textContent = data.cart_count ?? 1;
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(() => alert("❌ Error adding to cart."));
    });
});
</script>

<script>
document.querySelectorAll('.fav-btn').forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.dataset.productId;

        fetch('add_to_favorites.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("❤️ " + data.message);
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(() => alert("❌ Failed to add to favorites."));
    });
});
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
