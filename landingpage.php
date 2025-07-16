<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rekta Cycling</title>
  <link rel="stylesheet" href="landingCSS.css" />
  <script src="landingJS.js"></script>
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
          <a href="clothing.html"><img src="carousel1.png" alt="Men's Collection" /></a>
          <a href="cycling.html"><img src="carousel2.png" alt="Women's Collection" /></a>
          <a href="accessories.html"><img src="carousel3.png" alt="Accessories" /></a>
        </div>
        <button class="carousel-button prev" id="prevBtn">❮</button>
        <button class="carousel-button next" id="nextBtn">❯</button>
      </div>
      </div>
      <?php
// Connect to the database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "nickimnjaj"; // Replace with your actual DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all rows from nikitaminaj
$sql = "SELECT belly, nicki, ariana, dua FROM nikitaminaj";
$result = $conn->query($sql);
?>

<section class="products">
  <div class="container">
    <h2>Featured Products</h2>
    <div class="product-grid">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="product-card">
          <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
            <p><?php echo htmlspecialchars($row['belly']); ?></p>
            <span>$<?php echo number_format($row['nicki'], 2); ?></span>
            <!-- Optional: display dua or ariana if needed -->
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No products found.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php $conn->close(); ?>


    </div>
  </section>

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
  
</body>
</html>