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
            font-family: Milker;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-input {
            padding: 0.6rem 1rem;
            padding-right: 2.5rem;
            border: 2px solid #333;
            border-radius: 25px;
            background: transparent;
            color: white;
            font-size: 0.9rem;
            width: 200px;
            transition: all 0.3s;
        }
        
        .search-input::placeholder {
            color: #ccc;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #f15a24;
            background: rgba(255,255,255,0.1);
        }
        
        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #ccc;
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.3s;
        }
        
        .search-btn:hover {
            color: #f15a24;
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
            height: 60vh;
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
            margin: 0 auto 2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #f15a24;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            margin-top: 1rem;
            border-radius: 30px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn:hover {
            background: #000;
            transform: translateY(-2px);
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
        
        /* Products Section */
        .products-container {
            flex-grow: 1;
        }
        
        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .products-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #000;
        }
        
        .sort-select {
            padding: 0.6rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-weight: 500;
            background: white;
        }
        
        .products {
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
            background: #000;
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
            background: #f15a24;
            transform: translateY(-2px);
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
            .products {
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
            
            .products {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 1.5rem;
            }
            
            nav ul {
                gap: 1rem;
            }
            
            .nav-right {
                flex-direction: column;
                gap: 1rem;
            }
            
            .search-input {
                width: 180px;
            }
        }
        
        @media (max-width: 600px) {
            .products {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .products-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            @font-face {
                font-family: Milker;
                src: url(Milker.otf);
            }
        }
    </style>
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
            <li><a href="cycling.php">Cycling</a></li>
            <li><a href="accessories.php">Accessories</a></li>
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
            <img src="cart-73-16.png" alt="Cart" style="cursor: pointer;">
          </a>
          <a href="login.php">
            <img src="user.png" alt="User" style="cursor: pointer;">
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
