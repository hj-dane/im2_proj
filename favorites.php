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

// Handle remove from favorites action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    $product_id = $_POST['product_id'];
    // Here you would remove from database/session
    // For now, we'll just show a message
    $removed_message = "Product removed from favorites!";
}

// Sample favorites data (replace with database query)
$favorites = [
    [
        'id' => 1,
        'name' => 'Performance Cycling Cap',
        'price' => '₱499',
        'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
        'category' => 'Accessories',
        'type' => 'Headwear'
    ],
    [
        'id' => 2,
        'name' => 'Performance Track Jacket',
        'price' => '₱2,999',
        'image' => 'https://images.unsplash.com/photo-1542068829-1115f7259450?auto=format&fit=crop&w=400&q=80',
        'category' => 'Clothing',
        'type' => 'Jackets'
    ],
    [
        'id' => 3,
        'name' => 'Gel-Padded Cycling Gloves',
        'price' => '₱899',
        'image' => 'https://images.unsplash.com/photo-1520880867055-1e30d1cb001c?auto=format&fit=crop&w=400&q=80',
        'category' => 'Accessories',
        'type' => 'Protection'
    ],
    [
        'id' => 4,
        'name' => 'Cycling Hoodie',
        'price' => '₱2,199',
        'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=400&q=80',
        'category' => 'Clothing',
        'type' => 'Hoodies'
    ],
    [
        'id' => 5,
        'name' => 'Insulated Water Bottle',
        'price' => '₱349',
        'image' => 'https://images.unsplash.com/photo-1505839673365-e3971f8d9184?auto=format&fit=crop&w=400&q=80',
        'category' => 'Accessories',
        'type' => 'Hydration'
    ],
    [
        'id' => 6,
        'name' => 'Pro Cycling Jersey',
        'price' => '₱1,899',
        'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=400&q=80',
        'category' => 'Clothing',
        'type' => 'Jerseys'
    ]
];

// Filter favorites based on form submission
$filtered_favorites = $favorites;
$search_term = isset($_POST['search']) ? trim($_POST['search']) : '';
if ((isset($_POST['filter_favorites']) || $search_term !== '')) {
    $category_filter = $_POST['category'] ?? 'All Categories';
    $price_filter = $_POST['price'] ?? 'All Prices';
    $filtered_favorites = array_filter($favorites, function($favorite) use ($category_filter, $price_filter, $search_term) {
        $category_match = $category_filter === 'All Categories' || $favorite['category'] === $category_filter;
        // Price filter logic
        $price_match = true;
        $price_numeric = (int) filter_var(str_replace([',', '₱'], '', $favorite['price']), FILTER_SANITIZE_NUMBER_INT);
        if ($price_filter === '₱300 - ₱999') {
            $price_match = $price_numeric >= 300 && $price_numeric <= 999;
        } elseif ($price_filter === '₱1,000 - ₱2,000') {
            $price_match = $price_numeric >= 1000 && $price_numeric <= 2000;
        } elseif ($price_filter === '₱2,000+') {
            $price_match = $price_numeric > 2000;
        }
        // Search filter logic
        $search_match = true;
        if ($search_term !== '') {
            $search_match = stripos($favorite['name'], $search_term) !== false || stripos($favorite['category'], $search_term) !== false || stripos($favorite['type'], $search_term) !== false;
        }
        return $category_match && $price_match && $search_match;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKTA Cycling | Favorites</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="favorites.css">
</head>
<body>

  <header>
    <div class="container">
      <div class="nav-container" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
        
        <!-- Logo -->
        <div class="logo" style="font-size: 1.8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
          <a href="landingpage.php" style="color: white;">REKTA</a>
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
          <form action="#" method="GET" style="display: flex; align-items: center;">
            <input type="text" name="q" placeholder="Search products..." style="padding: 0.4rem 0.75rem; border: none; border-radius: 4px 0 0 4px;">
            <button type="submit" style="padding: 0.4rem 0.75rem; background: white; border: none; border-radius: 0 4px 4px 0; font-weight: bold; cursor: pointer;">
              🔍
            </button>
          </form>
          <a href="cart.php">
            <img src="cart-73-16.png" alt="Cart" style="cursor: pointer;">
          </a>
          <a href="login.php">
            <img src="user.png" alt="User" style="cursor: pointer;">
          </a>
          <a href="favorites.php">
            <img src="favorites.webp"  style="width: 16px; height: 16px; cursor: pointer;">
          </a>
        </div>
  
      </div>
    </div>
  </header>

<section class="hero">
    <div class="hero-content">
        <h1>Your Favorites</h1>
        <p>Keep track of your favorite cycling gear. Easy access to the products you love.</p>
    </div>
</section>

<div class="container">
    <?php if (isset($removed_message)): ?>
    <div class="success-message">
        <?php echo htmlspecialchars($removed_message); ?>
    </div>
    <?php endif; ?>

    <div class="main-content">
        <aside class="sidebar">
            <h2>Filter Favorites</h2>
            <form method="POST" action="">
                <label for="category-select">Category</label>
                <select id="category-select" name="category">
                    <option value="All Categories" <?php echo (isset($_POST['category']) && $_POST['category'] === 'All Categories') ? 'selected' : ''; ?>>All Categories</option>
                    <option value="Clothing" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                    <option value="Accessories" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                </select>

                <label for="price-select">Price Range</label>
                <select id="price-select" name="price">
                    <option value="All Prices" <?php echo (isset($_POST['price']) && $_POST['price'] === 'All Prices') ? 'selected' : ''; ?>>All Prices</option>
                    <option value="₱300 - ₱999" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱300 - ₱999') ? 'selected' : ''; ?>>₱300 - ₱999</option>
                    <option value="₱1,000 - ₱2,000" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱1,000 - ₱2,000') ? 'selected' : ''; ?>>₱1,000 - ₱2,000</option>
                    <option value="₱2,000+" <?php echo (isset($_POST['price']) && $_POST['price'] === '₱2,000+') ? 'selected' : ''; ?>>₱2,000+</option>
                </select>

                <button type="submit" name="filter_favorites" class="filter-btn">Apply Filters</button>
            </form>
        </aside>

        <section class="favorites-container">
            <div class="favorites-header">
                <h2>Your Favorites</h2>
                <div class="favorites-count"><?php echo count($filtered_favorites); ?> items</div>
            </div>

            <?php if (empty($filtered_favorites)): ?>
            <div class="no-favorites">
                <h3>No favorites yet</h3>
                <p>Start browsing our collection and add your favorite items to this list.</p>
                <a href="CLOTHING.php" class="browse-btn">Browse Clothing</a>
                <a href="accessories.php" class="browse-btn" style="margin-left: 1rem;">Browse Accessories</a>
            </div>
            <?php else: ?>
            <div class="favorites">
                <?php foreach ($filtered_favorites as $favorite): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <div class="favorite-badge">❤️</div>
                        <img src="<?php echo htmlspecialchars($favorite['image']); ?>" alt="<?php echo htmlspecialchars($favorite['name']); ?>" />
                        <div class="quick-view">Quick View</div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($favorite['name']); ?></h3>
                        <p><?php echo htmlspecialchars($favorite['price']); ?></p>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $favorite['id']; ?>">
                            <button type="submit" name="remove_favorite" onclick="return confirm('Remove from favorites?')">Remove from Favorites</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </div>
</div>

 <footer>
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; padding: 2rem 0;">
      
      <!-- Column 1: Shop -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Shop</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="mens.html" style="color: #fff;">Men</a></li>
          <li><a href="womens.html" style="color: #fff;">Women</a></li>
          <li><a href="#" style="color: #fff;">Accessories</a></li>
        </ul>
      </div>
  
      <!-- Column 2: Support -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Support</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="#" style="color: #fff;">Help</a></li>
          <li><a href="#" style="color: #fff;">Returns</a></li>
          <li><a href="#" style="color: #fff;">Order Tracker</a></li>
        </ul>
      </div>
  
      <!-- Column 3: Company Info -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Company</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="#" style="color: #fff;">About Us</a></li>
          <li><a href="#" style="color: #fff;">Careers</a></li>
          <li><a href="#" style="color: #fff;">Sustainability</a></li>
        </ul>
      </div>
  
      <!-- Column 4: Newsletter -->
      <div style="flex: 1 1 300px; margin-bottom: 1rem;">
        <h3>Sign up for updates</h3>
        <form>
          <input type="email" placeholder="Your email" style="padding: 0.5rem; width: 80%; margin-bottom: 0.5rem;">
          <br>
          <button type="submit" class="btn" style="background: #fff; color: #000;">Subscribe</button>
        </form>
      </div>

      <!-- Column 5: Social Media -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Follow Us</h3>
        <div style="display: flex; gap: 1rem; align-items: center; margin-inline: auto;">
          <a href="https://www.facebook.com/rektacycling"><img src="facebook.webp" alt="Facebook" style="width: 32px; height: 32px;"></a>
          <a href="https://www.instagram.com/rektacycling/"><img src="instagram.webp" alt="Instagram" style="width: 32px; height: 32px;"></a>
        </div>
      </div>

  
    <!-- Bottom Bar -->
    <div style="background-color: #111; text-align: center; padding: 1rem 0; width: 100%;">
      <p style="margin: 0;">&copy; 2025 Rekta Cycling. All rights reserved. |
        <a href="#" style="color: #fff;">Privacy</a> |
        <a href="#" style="color: #fff;">Terms</a>
      </p>
    </div>
  </footer>

<script>
// Quick view functionality
document.querySelectorAll('.quick-view').forEach(button => {
    button.addEventListener('click', function(e) {
        e.stopPropagation();
        // Implement quick view modal
        alert('Quick view feature coming soon!');
    });
});

// Add to cart functionality
function addToCart(productId) {
    alert('Product added to cart!');
    // You can implement AJAX call here to add to cart
}
</script>

</body>
</html>