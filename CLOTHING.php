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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gender_filter = $_POST['gender'] ?? 'All Genders';
    $category_filter = $_POST['category'] ?? 'All Categories';
    $price_filter = $_POST['price'] ?? 'All Prices';
    
    if ($gender_filter !== 'All Genders' || $category_filter !== 'All Categories' || $price_filter !== 'All Prices') {
        $filtered_products = array_filter($products, function($product) use ($gender_filter, $category_filter) {
            $gender_match = $gender_filter === 'All Genders' || $product['gender'] === $gender_filter;
            $category_match = $category_filter === 'All Categories' || $product['category'] === $category_filter;
            return $gender_match && $category_match;
        });
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKTA Cycling Clothing | Adidas Inspired</title>
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
                </ul>
            </nav>
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