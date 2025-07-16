<?php
// Database connection (you can modify these settings)
$host = 'localhost';
$dbname = 'rekta_cycling';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database connection fails, we'll use static data
    $pdo = null;
}

// Sample accessories data (replace with database query)
$accessories = [
    [
        'id' => 1,
        'name' => 'Performance Cycling Cap',
        'price' => '₱499',
        'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
        'category' => 'Headwear',
        'brand' => 'REKTA'
    ],
    [
        'id' => 2,
        'name' => 'Insulated Water Bottle',
        'price' => '₱349',
        'image' => 'https://images.unsplash.com/photo-1505839673365-e3971f8d9184?auto=format&fit=crop&w=400&q=80',
        'category' => 'Hydration',
        'brand' => 'REKTA'
    ],
    [
        'id' => 3,
        'name' => 'Gel-Padded Cycling Gloves',
        'price' => '₱899',
        'image' => 'https://images.unsplash.com/photo-1520880867055-1e30d1cb001c?auto=format&fit=crop&w=400&q=80',
        'category' => 'Protection',
        'brand' => 'REKTA'
    ],
    [
        'id' => 4,
        'name' => 'Waterproof Waist Bag',
        'price' => '₱1,299',
        'image' => 'https://images.unsplash.com/photo-1590080877777-f1ffb23d6e1d?auto=format&fit=crop&w=400&q=80',
        'category' => 'Storage',
        'brand' => 'REKTA'
    ],
    [
        'id' => 5,
        'name' => 'Compression Cycling Socks',
        'price' => '₱299',
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=400&q=80',
        'category' => 'Footwear',
        'brand' => 'REKTA'
    ],
    [
        'id' => 6,
        'name' => 'UV Protection Sunglasses',
        'price' => '₱1,599',
        'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=400&q=80',
        'category' => 'Protection',
        'brand' => 'REKTA'
    ],
    [
        'id' => 7,
        'name' => 'LED Bike Light Set',
        'price' => '₱799',
        'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?auto=format&fit=crop&w=400&q=80',
        'category' => 'Safety',
        'brand' => 'REKTA'
    ],
    [
        'id' => 8,
        'name' => 'Portable Bike Pump',
        'price' => '₱649',
        'image' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?auto=format&fit=crop&w=400&q=80',
        'category' => 'Tools',
        'brand' => 'REKTA'
    ]
];

// Filter accessories based on form submission
$filtered_accessories = $accessories;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_filter = $_POST['category'] ?? 'All Categories';
    $price_filter = $_POST['price'] ?? 'All Prices';
    $color_filter = $_POST['color'] ?? 'All Colors';
    $brand_filter = $_POST['brand'] ?? 'All Brands';
    
    if ($category_filter !== 'All Categories' || $price_filter !== 'All Prices' || $color_filter !== 'All Colors' || $brand_filter !== 'All Brands') {
        $filtered_accessories = array_filter($accessories, function($accessory) use ($category_filter, $brand_filter) {
            $category_match = $category_filter === 'All Categories' || $accessory['category'] === $category_filter;
            $brand_match = $brand_filter === 'All Brands' || $accessory['brand'] === $brand_filter;
            return $category_match && $brand_match;
        });
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKTA Cycling Accessories | Adidas Inspired</title>
    <link rel="stylesheet" href="../styles/landing.css">
    <link rel="stylesheet" href="../styles/accessories.css">
</head>
<body>

<header>
    <div class="container">
      <div class="nav-container" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
        
        <!-- Logo -->
        <div class="logo" style="font-size: 1.8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
          <a href="../index.php" style="color: white;">REKTA</a>
        </div>
  
        <!-- Centered Navigation Menu -->
        <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
          <ul style="display: flex; gap: 2rem; list-style: none;">
            <li><a href="clothing.php">Clothing</a></li>
            <li><a href="accessories.php">Accessories</a></li>
            <li><a href="contact us.php">Contact Us</a></li>
          </ul>
        </nav>
  
        <!-- Search / Cart / User -->
        <div style="display: flex; align-items: center; gap: 1rem;">
        <form class="search-bar-header" method="POST" action="">
                <input type="text" name="search" placeholder="Search accessories" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" autocomplete="off" />
                <button type="submit" aria-label="Search">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke="white" stroke-width="2" fill="none"/><line x1="16.5" y1="16.5" x2="21" y2="21" stroke="white" stroke-width="2"/></svg>
                </button>
            </form>
          <a href="cart.php">
            <img src="../assets/cart-73-16.png" alt="Cart" style="cursor: pointer;">
          </a>
          <a href="../login.php">
            <img src="../assets/user.png" alt="User" style="cursor: pointer;">
          </a>
          <a href="favorites.php">
            <img src="../assets/favorites.webp"  style="width: 16px; height: 16px; cursor: pointer;">
          </a>
        </div>
  
      </div>
    </div>
  </header>

<section class="hero">
    <div class="hero-content">
        <h1>Cycling Accessories</h1>
        <p>Premium gear for every ride. Discover our collection of high-performance cycling accessories designed for comfort and style.</p>
        <a href="#products" class="btn">Shop Now</a>
    </div>
</section>

<div class="container">
    <div class="main-content">
        <aside class="sidebar">
            <h2>Filter Products</h2>
            <form method="POST" action="">
                <label for="category-select">Category</label>
                <select id="category-select" name="category">
                    <option value="All Categories" <?php echo (isset($_POST['category']) && $_POST['category'] === 'All Categories') ? 'selected' : ''; ?>>All Categories</option>
                    <option value="Headwear" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Headwear') ? 'selected' : ''; ?>>Headwear</option>
                    <option value="Hydration" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Hydration') ? 'selected' : ''; ?>>Hydration</option>
                    <option value="Protection" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Protection') ? 'selected' : ''; ?>>Protection</option>
                    <option value="Storage" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Storage') ? 'selected' : ''; ?>>Storage</option>
                    <option value="Footwear" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Footwear') ? 'selected' : ''; ?>>Footwear</option>
                    <option value="Safety" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Safety') ? 'selected' : ''; ?>>Safety</option>
                    <option value="Tools" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Tools') ? 'selected' : ''; ?>>Tools</option>
                </select>

                <label for="price-select">Price Range</label>
                <select id="price-select" name="price">
                    <option value="All Prices" <?php echo (isset($_POST['price']) && $_POST['price'] === 'All Prices') ? 'selected' : ''; ?>>All Prices</option>
                    <option value="₱300 - ₱799" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱300 - ₱799') ? 'selected' : ''; ?>>₱300 - ₱799</option>
                    <option value="₱800 - ₱1,500" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱800 - ₱1,500') ? 'selected' : ''; ?>>₱800 - ₱1,500</option>
                    <option value="₱1,500 - ₱2,500" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱1,500 - ₱2,500') ? 'selected' : ''; ?>>₱1,500 - ₱2,500</option>
                    <option value="₱2,500+" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱2,500+') ? 'selected' : ''; ?>>₱2,500+</option>
                </select>

                <label for="color-select">Color</label>
                <select id="color-select" name="color">
                    <option value="All Colors" <?php echo (isset($_POST['color']) && $_POST['color'] === 'All Colors') ? 'selected' : ''; ?>>All Colors</option>
                    <option value="Black" <?php echo (isset($_POST['color']) && $_POST['color'] === 'Black') ? 'selected' : ''; ?>>Black</option>
                    <option value="White" <?php echo (isset($_POST['color']) && $_POST['color'] === 'White') ? 'selected' : ''; ?>>White</option>
                    <option value="Red" <?php echo (isset($_POST['color']) && $_POST['color'] === 'Red') ? 'selected' : ''; ?>>Red</option>
                    <option value="Blue" <?php echo (isset($_POST['color']) && $_POST['color'] === 'Blue') ? 'selected' : ''; ?>>Blue</option>
                    <option value="Green" <?php echo (isset($_POST['color']) && $_POST['color'] === 'Green') ? 'selected' : ''; ?>>Green</option>
                    <option value="Multi-color" <?php echo (isset($_POST['color']) && $_POST['color'] === 'Multi-color') ? 'selected' : ''; ?>>Multi-color</option>
                </select>

                <label for="brand-select">Brand</label>
                <select id="brand-select" name="brand">
                    <option value="All Brands" <?php echo (isset($_POST['brand']) && $_POST['brand'] === 'All Brands') ? 'selected' : ''; ?>>All Brands</option>
                    <option value="REKTA" <?php echo (isset($_POST['brand']) && $_POST['brand'] === 'REKTA') ? 'selected' : ''; ?>>REKTA</option>
                    <option value="Adidas" <?php echo (isset($_POST['brand']) && $_POST['brand'] === 'Adidas') ? 'selected' : ''; ?>>Adidas</option>
                    <option value="Nike" <?php echo (isset($_POST['brand']) && $_POST['brand'] === 'Nike') ? 'selected' : ''; ?>>Nike</option>
                    <option value="Specialized" <?php echo (isset($_POST['brand']) && $_POST['brand'] === 'Specialized') ? 'selected' : ''; ?>>Specialized</option>
                </select>

                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </aside>

        <section class="products-container" id="products">
            <div class="products-header">
                <h2>All Accessories</h2>
                <select class="sort-select">
                    <option>Sort by: Featured</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Newest First</option>
                    <option>Best Selling</option>
                </select>
            </div>

            <div class="products">
                <?php foreach ($filtered_accessories as $accessory): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($accessory['image']); ?>" alt="<?php echo htmlspecialchars($accessory['name']); ?>" />
                        <div class="quick-view">Quick View</div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($accessory['name']); ?></h3>
                        <p><?php echo htmlspecialchars($accessory['price']); ?></p>
                        <button onclick="addToCart(<?php echo $accessory['id']; ?>)">Add to Cart</button>
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

// Search functionality
function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const productCards = document.querySelectorAll('.product-card');
    
    if (searchTerm === '') {
        // Show all products if search is empty
        productCards.forEach(card => {
            card.style.display = 'block';
        });
        return;
    }
    
    productCards.forEach(card => {
        const productName = card.querySelector('h3').textContent.toLowerCase();
        const productCategory = card.querySelector('p').textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Add enter key functionality to search input
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// Real-time search as user types
document.getElementById('searchInput').addEventListener('input', function() {
    performSearch();
});
</script>

</body>
</html>
