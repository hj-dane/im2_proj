<?php
// Database connection (you can modify these settings)
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database connection fails, we'll use static data
    $pdo = null;
}

// Sample product data (replace with database query)
$products = [
    [
        'id' => 1,
        'name' => 'Performance T-Shirt',
        'price' => '₱999',
        'image' => 'https://images.unsplash.com/photo-1521334884684-d80222895322?auto=format&fit=crop&w=400&q=80',
        'category' => 'T-Shirts',
        'gender' => 'Unisex'
    ],
    [
        'id' => 2,
        'name' => 'Cycling Hoodie',
        'price' => '₱2,199',
        'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=400&q=80',
        'category' => 'Hoodies',
        'gender' => 'Unisex'
    ],
    [
        'id' => 3,
        'name' => 'Performance Track Jacket',
        'price' => '₱2,999',
        'image' => 'https://images.unsplash.com/photo-1542068829-1115f7259450?auto=format&fit=crop&w=400&q=80',
        'category' => 'Jackets',
        'gender' => 'Unisex'
    ],
    [
        'id' => 4,
        'name' => 'Cycling Shorts',
        'price' => '₱1,199',
        'image' => 'https://images.unsplash.com/photo-1520975698404-c4a091c5b0a2?auto=format&fit=crop&w=400&q=80',
        'category' => 'Shorts',
        'gender' => 'Unisex'
    ],
    [
        'id' => 5,
        'name' => 'Pro Cycling Jersey',
        'price' => '₱1,899',
        'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=400&q=80',
        'category' => 'Jerseys',
        'gender' => 'Unisex'
    ],
    [
        'id' => 6,
        'name' => 'Cycling Pants',
        'price' => '₱2,499',
        'image' => 'https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?auto=format&fit=crop&w=400&q=80',
        'category' => 'Pants',
        'gender' => 'Unisex'
    ]
];

// Filter products based on form submission
$filtered_products = $products;
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' ||
    (isset($_GET['search']) && $_GET['search'] !== '')
) {
    $gender_filter = $_POST['gender'] ?? 'All Genders';
    $category_filter = $_POST['category'] ?? 'All Categories';
    $price_filter = $_POST['price'] ?? 'All Prices';
    $search_term = isset($_POST['search']) ? trim($_POST['search']) : (isset($_GET['search']) ? trim($_GET['search']) : '');
    
    $filtered_products = array_filter($products, function($product) use ($gender_filter, $category_filter, $price_filter, $search_term) {
        $gender_match = $gender_filter === 'All Genders' || $product['gender'] === $gender_filter;
        $category_match = $category_filter === 'All Categories' || $product['category'] === $category_filter;
        // Price filter logic
        $price_match = true;
        $price_numeric = (int) filter_var(str_replace([',', '₱'], '', $product['price']), FILTER_SANITIZE_NUMBER_INT);
        if ($price_filter === '₱500 - ₱999') {
            $price_match = $price_numeric >= 500 && $price_numeric <= 999;
        } elseif ($price_filter === '₱1,000 - ₱2,000') {
            $price_match = $price_numeric >= 1000 && $price_numeric <= 2000;
        } elseif ($price_filter === '₱2,000 - ₱3,500') {
            $price_match = $price_numeric >= 2000 && $price_numeric <= 3500;
        } elseif ($price_filter === '₱3,500+') {
            $price_match = $price_numeric > 3500;
        }
        // Search filter logic
        $search_match = true;
        if ($search_term !== '') {
            $search_match = stripos($product['name'], $search_term) !== false || stripos($product['category'], $search_term) !== false;
        }
        return $gender_match && $category_match && $price_match && $search_match;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKTA Cycling | Clothing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/clothing.css">
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
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search clothing..." id="searchInput">
                    <button class="search-btn" onclick="performSearch()">🔍</button>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>Cycling Clothing</h1>
        <p>Performance gear designed for every ride. Discover our premium collection of cycling apparel that combines style, comfort, and functionality.</p>
        <a href="#products" class="btn">Shop Now</a>
    </div>
</section>

<div class="container">
    <div class="main-content">
        <aside class="sidebar">
            <h2>Filter Products</h2>
            <form method="POST" action="">
                <input type="hidden" name="search" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                <label for="gender-select">Gender</label>
                <select id="gender-select" name="gender">
                    <option value="All Genders" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'All Genders') ? 'selected' : ''; ?>>All Genders</option>
                    <option value="Men" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Men') ? 'selected' : ''; ?>>Men</option>
                    <option value="Women" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Women') ? 'selected' : ''; ?>>Women</option>
                    <option value="Unisex" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                </select>

                <label for="category-select">Category</label>
                <select id="category-select" name="category">
                    <option value="All Categories" <?php echo (isset($_POST['category']) && $_POST['category'] === 'All Categories') ? 'selected' : ''; ?>>All Categories</option>
                    <option value="T-Shirts" <?php echo (isset($_POST['category']) && $_POST['category'] === 'T-Shirts') ? 'selected' : ''; ?>>T-Shirts</option>
                    <option value="Jackets" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Jackets') ? 'selected' : ''; ?>>Jackets</option>
                    <option value="Hoodies" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Hoodies') ? 'selected' : ''; ?>>Hoodies</option>
                    <option value="Shorts" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Shorts') ? 'selected' : ''; ?>>Shorts</option>
                    <option value="Jerseys" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Jerseys') ? 'selected' : ''; ?>>Jerseys</option>
                    <option value="Pants" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Pants') ? 'selected' : ''; ?>>Pants</option>
                </select>

                <label for="price-select">Price Range</label>
                <select id="price-select" name="price">
                    <option value="All Prices" <?php echo (isset($_POST['price']) && $_POST['price'] === 'All Prices') ? 'selected' : ''; ?>>All Prices</option>
                    <option value="₱500 - ₱999" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱500 - ₱999') ? 'selected' : ''; ?>>₱500 - ₱999</option>
                    <option value="₱1,000 - ₱2,000" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱1,000 - ₱2,000') ? 'selected' : ''; ?>>₱1,000 - ₱2,000</option>
                    <option value="₱2,000 - ₱3,500" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱2,000 - ₱3,500') ? 'selected' : ''; ?>>₱2,000 - ₱3,500</option>
                    <option value="₱3,500+" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱3,500+') ? 'selected' : ''; ?>>₱3,500+</option>
                </select>

                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </aside>

        <section class="products-container" id="products">
            <div class="products-header">
                <h2>All Clothing</h2>
                <select class="sort-select">
                    <option>Sort by: Featured</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Newest First</option>
                    <option>Best Selling</option>
                </select>
            </div>

            <div class="products">
                <?php foreach ($filtered_products as $product): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        <div class="quick-view">Quick View</div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['price']); ?></p>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<footer>
    <div class="container">
        &copy; 2025 REKTA Cycling. All rights reserved.
    </div>
</footer>

<script>
function addToCart(productId) {
    // Add to cart functionality
    alert('Product added to cart!');
    // You can implement AJAX call here to add to cart
}

// Quick view functionality
document.querySelectorAll('.quick-view').forEach(button => {
    button.addEventListener('click', function(e) {
        e.stopPropagation();
        // Implement quick view modal
        alert('Quick view feature coming soon!');
    });
});
</script>

</body>
</html> 