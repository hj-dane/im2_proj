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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_favorites'])) {
    $category_filter = $_POST['category'] ?? 'All Categories';
    $price_filter = $_POST['price'] ?? 'All Prices';
    
    if ($category_filter !== 'All Categories' || $price_filter !== 'All Prices') {
        $filtered_favorites = array_filter($favorites, function($favorite) use ($category_filter) {
            return $category_filter === 'All Categories' || $favorite['category'] === $category_filter;
        });
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKTA Cycling Favorites | Adidas Inspired</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Helvetica", sans-serif;
            background-color: #fff;
            color: #111;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            background: #000;
            color: #fff;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            position: relative;
        }
        
        nav a:hover {
            color: #f15a24;
        }
        
        nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #f15a24;
            transition: width 0.3s;
        }
        
        nav a:hover::after {
            width: 100%;
        }
        
        .hero {
            background: linear-gradient(135deg, #000 0%, #333 100%);
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            margin-bottom: 3rem;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Main Content */
        .main-content {
            display: flex;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #f8f8f8;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        
        .sidebar h2 {
            margin-bottom: 2rem;
            font-size: 1.4rem;
            border-bottom: 3px solid #f15a24;
            padding-bottom: 0.8rem;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar label {
            display: block;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .sidebar select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s;
        }
        
        .sidebar select:focus {
            border-color: #f15a24;
            outline: none;
            box-shadow: 0 0 0 3px rgba(241, 90, 36, 0.1);
        }
        
        .filter-btn {
            width: 100%;
            padding: 0.8rem;
            background: #000;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1.5rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .filter-btn:hover {
            background: #f15a24;
        }
        
        /* Favorites Section */
        .favorites-container {
            flex-grow: 1;
        }
        
        .favorites-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .favorites-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #000;
        }
        
        .favorites-count {
            color: #666;
            font-size: 1.1rem;
        }
        
        .favorites {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2.5rem;
        }
        
        .product-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.4s ease;
            cursor: pointer;
            border: 1px solid #f0f0f0;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #f15a24;
        }
        
        .product-image-container {
            position: relative;
            overflow: hidden;
            background: #f8f8f8;
            aspect-ratio: 1;
        }
        
        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .product-card:hover img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-card h3 {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
            color: #111;
            line-height: 1.3;
        }
        
        .product-card p {
            font-weight: 700;
            color: #f15a24;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }
        
        .product-card button {
            background: #c62828;
            color: #fff;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .product-card button:hover {
            background: #a40000;
            transform: translateY(-2px);
        }
        
        /* Favorite Badge */
        .favorite-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f15a24;
            color: white;
            padding: 0.5rem;
            border-radius: 50%;
            font-size: 1.2rem;
            z-index: 10;
        }
        
        /* Quick View Overlay */
        .quick-view {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .product-image-container:hover .quick-view {
            opacity: 1;
        }
        
        /* No Favorites Message */
        .no-favorites {
            text-align: center;
            color: #777;
            margin-top: 4rem;
            font-size: 1.5rem;
            padding: 3rem;
            background: #f8f8f8;
            border-radius: 12px;
        }
        
        .no-favorites h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .no-favorites p {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .browse-btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #f15a24;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 30px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .browse-btn:hover {
            background: #000;
            transform: translateY(-2px);
        }
        
        /* Success Message */
        .success-message {
            background: #4caf50;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }
        
        /* Footer */
        footer {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .favorites {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 2rem;
            }
        }
        
        @media (max-width: 900px) {
            .main-content {
                flex-direction: column;
                gap: 2rem;
            }
            
            .sidebar {
                width: 100%;
                position: static;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .favorites {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 1.5rem;
            }
            
            nav ul {
                gap: 1rem;
            }
        }
        
        @media (max-width: 600px) {
            .favorites {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .favorites-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <div class="nav-container">
            <div class="logo">REKTA Cycling</div>
            <nav>
                <ul>
                    <li><a href="#">Men</a></li>
                    <li><a href="#">Women</a></li>
                    <li><a href="#">Kids</a></li>
                    <li><a href="#">Clothing</a></li>
                    <li><a href="#">Accessories</a></li>
                    <li><a href="#">Favorites</a></li>
                </ul>
            </nav>
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
    <div class="container">
        &copy; 2025 REKTA Cycling. All rights reserved.
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