<?php
// Connect to the database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "school_db"; // Changed to match your SQL dump

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the correct table (product_inventory)
$sql = "SELECT product_name, unit_price, product_description FROM product_inventory WHERE is_active = 1 LIMIT 6";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rekta Cycling</title>
  <link rel="stylesheet" href="styles/landingCSS.css" />
  <script src="landingJS.js"></script>
</head>
<body>

  <header>
    <div class="container">
      <div class="nav-container" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
        
        <!-- Logo -->
        <div class="logo" style="font-size: 1.8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
          <a href="index.php" style="color: white;">REKTA</a>
        </div>
  
        <!-- Centered Navigation Menu -->
        <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
          <ul style="display: flex; gap: 2rem; list-style: none;">
            <li><a href="customer/clothing.php">Clothing</a></li>
            <li><a href="customer/cycling.php">Cycling</a></li>
            <li><a href="customer/accessories.php">Accessories</a></li>
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
          <a href="customer/cart.php">
            <img src="assets/cart-73-16.png" alt="Cart" style="cursor: pointer;">
          </a>
          <a href="login.php">
            <img src="assets/user.png" alt="User" style="cursor: pointer;">
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
      <h1>Ride With Style</h1>
      <p>Find the perfect REKTA item for you.</p>
      <br>
      <a href="#" class="btn">Shop Now</a>
    </div>
  </section>

  <section class="products">
    <div class="container">
      <br>
      <h2>Featured Products</h2>
      <div class="carousel">
        <div class="carousel-images" id="carouselImages">
          <a href="customer/clothing.php"><img src="assets/carousel1.png" alt="Clothing" /></a>
          <a href="customer/accessories.php"><img src="assets/carousel3.png" alt="Accessories" /></a>
        </div>
        <button class="carousel-button prev" id="prevBtn">❮</button>
        <button class="carousel-button next" id="nextBtn">❯</button>
      </div>
    </div>
  </section>

  <section class="products">
    <div class="container">
      <h2>Our Collection</h2>
      <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
              <img src="assets/product-placeholder.jpg" alt="Product Image">
              <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
              <p><?php echo htmlspecialchars($row['product_description']); ?></p>
              <span>$<?php echo number_format($row['unit_price'], 2); ?></span>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No products found.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php $conn->close(); ?>

  <footer>
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; padding: 2rem 0;">
      
      <!-- Column 1: Shop -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Shop</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="customer/clothing.php">Clothing</a></li>
          <li><a href="customer/accessories.php">Accessories</a></li>
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
  
</body>
</html>